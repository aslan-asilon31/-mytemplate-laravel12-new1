<?php

namespace App\Livewire\MsCabang;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\RoleHasPermission;
use App\Models\MsCabang;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Livewire\MsCabang\Forms\MsCabangForm;
use Mary\Traits\Toast;
use App\Helpers\Permission\Traits\WithPermission;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;


class MsCabangDaftar extends Component
{

  public string $title = "Cabang";
  public string $url = "/cabang";


  use WithPermission;

  #[\Livewire\Attributes\Locked]
  public $id;

  use Toast;
  use WithPagination;

  #[Url(except: '')]
  public ?string $search = '';

  public bool $filterDrawer;


  public array $sortBy = ['column' => 'nama', 'direction' => 'desc'];

  #[Url(except: '')]
  public array $filters = [];
  public array $filterForm = [
    'nama' => '',
    'nomor' => '',
    'dibuat_oleh' => '',
    'diupdate_oleh' => '',

  ];


  public function mount()
  {
    // $this->permission('cabang-list');
  }

  #[Computed]
  public function headers(): array
  {
    return [
      ['key' => 'action', 'label' => 'Action', 'sortable' => false, 'class' => 'whitespace-nowrap border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'nomor', 'label' => '#', 'sortable' => false, 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-right'],
      ['key' => 'id', 'label' => 'ID', 'sortBy' => 'id', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'nama', 'label' => 'Nama', 'sortBy' => 'nama', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'dibuat_oleh', 'label' => 'Dibuat Oleh',  'sortBy' => 'dibuat_oleh', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'diupdate_oleh', 'label' => 'Diupdate Oleh',  'sortBy' => 'diupdate_oleh', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'tgl_dibuat', 'label' => 'Tanggal Dibuat', 'format' => ['date', 'Y-m-d H:i:s'], 'sortBy' => 'tgl_dibuat', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'tgl_diupdate', 'label' => 'Tanggao Diupdate', 'format' => ['date', 'Y-m-d H:i:s'], 'sortBy' => 'tgl_diupdate', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'status', 'label' => 'Status', 'format' => ['date', 'Y-m-d H:i:s'], 'sortBy' => 'status', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center']
    ];
  }

  #[Computed]
  public function rows(): LengthAwarePaginator
  {
    $query = MsCabang::query();

    $query->when($this->search, fn($q) => $q->where('nama', 'like', "%{$this->search}%"))
      ->when(($this->filters['nama'] ?? ''), fn($q) => $q->where('nama', 'like', "%{$this->filters['nama']}%"))
      ->when(($this->filters['nomor'] ?? ''), fn($q) => $q->where('nomor', 'like', "%{$this->filters['nomor']}%"))
      ->when(($this->filters['dibuat_oleh'] ?? ''), fn($q) => $q->where('dibuat_oleh', 'like', "%{$this->filters['dibuat_oleh']}%"))
      ->when(($this->filters['diupdate_oleh'] ?? ''), fn($q) => $q->where('diupdate_oleh', 'like', "%{$this->filters['diupdate_oleh']}%"))
      ->when(($this->filters['tgl_dibuat'] ?? ''), function ($q) {
        $dateTime = $this->filters['tgl_dibuat'];
        $dateOnly = substr($dateTime, 0, 10);
        $q->whereDate('tgl_dibuat', $dateOnly);
      })
      ->when(($this->filters['tgl_diupdate'] ?? ''), function ($q) {
        $dateTime = $this->filters['tgl_diupdate'];
        $dateOnly = substr($dateTime, 0, 10);
        $q->whereDate('tgl_diupdate', $dateOnly);
      })
      ->when(($this->filters['status'] ?? ''), fn($q) => $q->where('status', 'like', "%{$this->filters['status']}%"));

    $paginator = $query
      ->orderBy('nomor', 'asc')
      ->paginate(20);

    $start = ($paginator->currentPage() - 1) * $paginator->perPage();

    $paginator->getCollection()->transform(function ($item, $key) use ($start) {
      return $item;
    });

    return $paginator;
  }

  public function filter()
  {
    $validatedFilters = $this->validate(
      [
        'filterForm.nama' => 'nullable|string',
        'filterForm.dibuat_oleh' => 'nullable|string',
        'filterForm.diupdate_oleh' => 'nullable|string',
        'filterForm.tgl_dibuat' => 'nullable|string',
        'filterForm.tgl_diupdate' => 'nullable|string',
        'filterForm.status' => 'nullable|integer',
      ],
      [],
      [
        'filterForm.nama' => 'Nama',
        'filterForm.dibuat_oleh' => 'Dibuat Oleh',
        'filterForm.diupdate_oleh' => 'Diupdate Oleh',
        'filterForm.tgl_dibuat' => 'Tanggal Dibuat',
        'filterForm.tgl_diupdate' => 'Tanggal Diupdate',
        'filterForm.status' => 'Status',
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

  public function delete()
  {
    $masterData = MsCabang::findOrFail($this->id);

    \Illuminate\Support\Facades\DB::beginTransaction();
    try {

      $this->deleteImage($masterData['image_url']);

      $masterData->delete();
      \Illuminate\Support\Facades\DB::commit();

      $this->success('Data Berhasil dihapus');
      $this->redirect($this->url, true);
    } catch (\Throwable $th) {
      \Illuminate\Support\Facades\DB::rollBack();
      $this->error('Data Gagal dihapus');
    }
  }

  public function render()
  {
    return view('livewire.master-cabang.master-cabang-daftar')
      ->title($this->title);
  }
}
