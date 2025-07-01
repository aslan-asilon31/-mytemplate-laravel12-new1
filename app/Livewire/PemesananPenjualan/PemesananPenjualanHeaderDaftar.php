<?php

namespace App\Livewire\PemesananPenjualan;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\TrPemesananPenjualanHeader;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Livewire\ProductResources\Forms\ProductForm;
use Mary\Traits\Toast;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Permission\Traits\WithPermission;


class PemesananPenjualanHeaderDaftar extends Component
{

  public string $title = "Pemesanan Penjualan List";
  public string $url = "/pemesanan-penjualan";


  #[\Livewire\Attributes\Locked]
  public $id;

  use Toast;
  use WithPagination;
  use WithPermission;
  // use \App\Helpers\Permission\Traits\HasAccess;

  #[Url(except: '')]
  public ?string $search = '';

  public bool $filterDrawer;

  public array $sortBy = ['column' => 'nama', 'direction' => 'desc'];

  #[Url(except: '')]
  public array $filters = [];
  public array $filterForm = [
    'nama' => '',
    'tgl_dibuat' => '',
    'status' => '',
  ];



  public function boot()
  {
    $halamanId = \App\Models\HakAkses::where('nama', 'tr_pemesanan_penjualan-daftar')->value('id');

    \Illuminate\Support\Facades\Gate::authorize('daftar', [
      \App\Models\HakAkses::class,
      $halamanId,
    ]);
  }

  public function mount() {}

  #[Computed]
  public function headers(): array
  {
    return [
      ['key' => 'action', 'label' => 'Action', 'sortable' => false, 'class' => 'whitespace-nowrap border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'id', 'label' => 'ID', 'sortBy' => 'id', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'nama', 'label' => 'Nama Pemesanan Penjualan', 'sortBy' => 'nama', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'ms_pelanggan_id', 'label' => 'ID Pelanggan', 'sortBy' => 'ms_pelanggan_id', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'nama_pelanggan', 'label' => 'Pelanggan', 'sortBy' => 'nama_pelanggan', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'ms_cabang_id', 'label' => 'ID Cabang', 'sortBy' => 'ms_cabang_id', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'nama_cabang', 'label' => 'Cabang', 'sortBy' => 'nama_cabang', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'status', 'label' => 'Status', 'sortBy' => 'status', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'tgl_dibuat', 'label' => 'Tanggal Dibuat', 'format' => ['date', 'Y-m-d H:i:s'], 'sortBy' => 'tgl_dibuat', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center']
    ];
  }

  #[Computed]
  public function rows(): LengthAwarePaginator
  {
    $query = TrPemesananPenjualanHeader::with('msPelanggan');

    $query->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%"))
      ->when(($this->filters['nama'] ?? ''), fn($q) => $q->where('nama', 'like', "%{$this->filters['nama']}%"))
      ->when(($this->filters['ms_pelanggan_id'] ?? ''), fn($q) => $q->where('ms_pelanggan_id', 'like', "%{$this->filters['ms_pelanggan_id']}%"))
      ->when(($this->filters['nama_pelanggan'] ?? ''), fn($q) => $q->where('nama_pelanggan', 'like', "%{$this->filters['nama_pelanggan']}%"))
      ->when(($this->filters['ms_cabang_id'] ?? ''), fn($q) => $q->where('ms_cabang_id', 'like', "%{$this->filters['ms_cabang_id']}%"))
      ->when(($this->filters['nama_cabang'] ?? ''), fn($q) => $q->where('nama_cabang', 'like', "%{$this->filters['nama_cabang']}%"))
      ->when(($this->filters['status'] ?? ''), fn($q) => $q->where('status', $this->filters['status']))
      ->when(($this->filters['tgl_dibuat'] ?? ''), function ($q) {
        $dateTime = $this->filters['tgl_dibuat'];
        $dateOnly = substr($dateTime, 0, 10);
        $q->whereDate('tgl_dibuat', $dateOnly);
      });

    $paginator = $query
      // ->whereIn('ms_cabang_id', array_unique($this->aksesGudang()->pluck('ms_cabang_id')->toArray()))
      ->paginate(20);

    $start = ($paginator->currentPage() - 1) * $paginator->perPage();

    $paginator->getCollection()->transform(function ($item, $key) use ($start) {
      $item->nama_pelanggan = optional($item->msPelanggan)->nama;
      $item->nama_cabang = optional($item->msCabang)->nama;
      return $item;
    });

    return $paginator;
  }

  public function filter()
  {
    $validatedFilters = $this->validate(
      [
        'filterForm.nama' => 'nullable|string',
        'filterForm.status' => 'nullable|integer',
        'filterForm.tgl_dibuat' => 'nullable|string',
      ],
      [],
      [
        'filterForm.nama' => 'Nama',
        'filterForm.status' => 'Status',
        'filterForm.tgl_dibuat' => 'Tanggal Dibuat',
      ]
    )['filterForm'];

    $this->filters = collect($validatedFilters)->reject(fn($value) => $value === '')->toArray();
    $this->success('Filter Result');
    $this->filterDrawer = false;
  }

  public function clear(): void
  {
    $this->reset('filters');
    $this->reset('filterForm');
    $this->success('filter cleared');
  }


  public function render()
  {

    return view('livewire.pemesanan-penjualan.pemesanan-penjualan-header-daftar')
      ->title($this->title);
  }
}
