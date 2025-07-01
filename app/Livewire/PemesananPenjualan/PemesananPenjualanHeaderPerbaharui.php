<?php

namespace App\Livewire\PemesananPenjualan;

use App\Livewire\PemesananPenjualan\Forms\PemesananPenjualanHeaderForm;
use App\Livewire\PemesananPenjualan\Forms\PemesananPenjualanDetailForm;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\TrPemesananPenjualanDetail;
use App\Models\MsAksi;
use App\Models\PegawaiAksesCabang;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Helpers\Permission\Traits\WithPermission;
// use App\Helpers\FormHook\Traits\WithPemesananPenjualan;
use App\Models\TrPemesananPenjualanHeader;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use App\Models\MsStatus;
use Spatie\Permission\Models\Permission;
use App\Models\RoleAksesStatus;

class PemesananPenjualanHeaderPerbaharui extends Component
{

  public function render()
  {
    return view('livewire.pemesanan-penjualan.pemesanan-penjualan-header-perbaharui')
      ->title($this->title);
  }

  use \Livewire\WithFileUploads;
  use \Mary\Traits\Toast;
  use WithPermission;
  // use WithPemesananPenjualan;
  // use \App\Helpers\Permission\Traits\HasAccess;
  use \App\Helpers\FormHook\Traits\AksesOpsi;

  public Collection $pencarianPelanggan;
  public Collection $pencarianCabang;
  public Collection $pencarianBarang;
  public Collection $pencarianRak;

  public bool $modalDetail = false;
  public array $validatedForm = [];
  public array $validatedFormDetail = [];
  public array $headers = [];

  #[\Livewire\Attributes\Locked]
  public string $title = 'Pemesanan Penjualan';

  #[Url(except: '')]
  public ?string $search = '';

  public  $brands = [];
  public int $detailIndex;

  public $selectedBrandId = '';
  public array $details = [];
  public $selectedProductCategoryFirstId = '';
  public $productCategoryFirsts = [];
  public $productCategories = [];
  public $header;
  public $halaman = 'pemesanan-penjualan';
  public $cabang;
  public $detailId = null;

  #[\Livewire\Attributes\Locked]
  public string $url = '/products';

  #[\Livewire\Attributes\Locked]
  private string $baseFolderName = '/files/images/products';

  #[\Livewire\Attributes\Locked]
  private string $baseImageName = 'product_image';

  #[\Livewire\Attributes\Locked]
  public string $id = '';

  #[\Livewire\Attributes\Locked]
  public string $readonly = '';

  #[\Livewire\Attributes\Locked]
  public bool $isReadonly = false;

  #[\Livewire\Attributes\Locked]
  public bool $isDisabled = false;

  #[\Livewire\Attributes\Locked]
  public array $options = [];

  #[\Livewire\Attributes\Locked]
  public array $optionStatus = [];

  #[\Livewire\Attributes\Locked]
  public array $optionCabang = [];

  #[\Livewire\Attributes\Locked]
  public array $optionPegawai = [];

  #[\Livewire\Attributes\Locked]
  public array $optionPelanggan = [];


  #[\Livewire\Attributes\Locked]
  protected $headerModel = \App\Models\TrPemesananPenjualanHeader::class;

  #[\Livewire\Attributes\Locked]
  protected $detailModel = \App\Models\TrPemesananPenjualanDetail::class;


  public array $statusDropdownAktif = [];
  public $pegawai;


  // #[\Livewire\Attributes\Session]
  // public $search;

  public PemesananPenjualanHeaderForm $headerForm;
  public PemesananPenjualanDetailForm $detailForm;





  public function mount()
  {

    $this->cariPelanggan();
    $this->cariCabang();
    $this->cariBarang();


    if ($this->id && $this->readonly) {
      $this->title .= ' (Tampil)';
      $this->tampil();
    } else if ($this->id) {
      $this->title .= ' (Edit)';
      $this->edit();
    } else {
      $this->title .= ' (Buat)';
      $this->buat();
    }
  }


  public function simpan()
  {

    $validatedHeaderForm = $this->validate(
      $this->headerForm->rules(),
      [],
      $this->headerForm->attributes()
    )['headerForm'];

    $halaman = 'tr_pemesanan_penjualan-simpan';
    $msCabangId = $validatedHeaderForm['ms_cabang_id'];
    $statusId = $validatedHeaderForm['status_option'];
    $halamanId = \App\Models\HakAkses::where('nama', $halaman)->value('id');

    $cekHakAkses = \Illuminate\Support\Facades\Gate::authorize('simpan', [
      \App\Models\HakAkses::class,
      $halamanId,
      $msCabangId,
      $statusId,
    ]);

    if (!$cekHakAkses) {
      abort(403, 'Akses halaman untuk menyimpan data ditolak.');
    }


    \Illuminate\Support\Facades\DB::beginTransaction();
    try {

      $validatedHeaderForm['dibuat_oleh'] = \Illuminate\Support\Facades\Auth::guard('pegawai')->user()->nama;
      $validatedHeaderForm['diupdate_oleh'] = \Illuminate\Support\Facades\Auth::guard('pegawai')->user()->nama;

      $header = $this->headerModel::create($validatedHeaderForm);

      $details = collect($this->details)
        ->map(function ($detail, $index) use ($header) {
          return [
            'id' => str()->orderedUuid()->toString(),
            'ms_barang_id' => $detail['ms_barang_id'],
            'ms_rak_id' => $detail['ms_rak_id'],
            'catatan' => $detail['catatan'],
            'qty' => $detail['qty'],
            'tgl_dibuat' => $detail['tgl_dibuat'],
            'tgl_diupdate' => now(),
            'dibuat_oleh' => $detail['dibuat_oleh'],
            'diupdate_oleh' => $detail['diupdate_oleh'],
          ];
        })
        ->toArray();

      $detail = $this->detailModel::insert($details);
      $this->headerForm->reset();
      $this->reset('details');
      \Illuminate\Support\Facades\DB::commit();
      $this->redirect('/pemesanan-penjualan', true);
      $this->success('Data has been stored');
    } catch (\Throwable $th) {
      \Illuminate\Support\Facades\DB::rollBack();
      \Log::error('Data product failed to store: ' . $th->getMessage());

      $this->error('Data failed to store');
    }
  }

  public function simpanDetail()
  {
    $this->detailForm->id = str()->orderedUuid()->toString();
    $this->detailForm->tgl_dibuat = now();
    $this->detailForm->dibuat_oleh = 'admin';
    $this->detailForm->diupdate_oleh = 'admin';

    $validatedDetailForm = $this->validate(
      $this->detailForm->rules(),
      [],
      $this->detailForm->attributes()
    )['detailForm'];

    $this->details[] =  $validatedDetailForm;
    $this->modalDetail = false;
    $this->reset('detailForm');
    $this->success('Data berhasil dibuat');
  }


  public function editDetail($detailIndex)
  {
    $detailData = $this->details[$detailIndex];
    $this->detailForm->fill($detailData);
    $this->detailId = $this->detailForm->id;

    $this->detailIndex = $detailIndex;
    return $this->modalDetail = true;
  }

  public function updateDetail()
  {
    $validatedDetailForm = $this->validate(
      $this->detailForm->rules(),
      [],
      $this->detailForm->attributes()
    )['detailForm'];

    $this->details[$this->detailIndex] = $this->detailForm->toArray();

    $this->reset(['detailForm', 'detailIndex']);
    $this->modalDetail = false;
    $this->success('Data berhasil diupdate');
  }

  public function hapusDetail($detailIndex)
  {
    // unset($this->details[$detailIndex]);
    // $this->details = array_values($this->details);
    // $this->success('Sales Order Detail Deleted.');
  }


  public function buat()
  {
    $halamanId = \App\Models\HakAkses::where('nama', 'tr_pemesanan_penjualan-buat')->value('id');
    \Illuminate\Support\Facades\Gate::authorize('buat', [
      \App\Models\HakAkses::class,
      $halamanId,
    ]);


    $this->headerForm->reset();
    $this->detailForm->reset();
  }

  public function buatDetail()
  {
    $this->detailForm->reset();
    $this->modalDetail = true;
  }



  public function tampil()
  {
    $halaman = 'tr_pemesanan_penjualan-tampil';
    $halamanId = \App\Models\HakAkses::where('nama', $halaman)->value('id');

    \Illuminate\Support\Facades\Gate::authorize('lihat', [
      \App\Models\HakAkses::class,
      $halamanId,
    ]);
    $this->isReadonly = true;
    $this->isDisabled = true;
    $masterHeaderData = $this->headerModel::findOrFail($this->id);
    $this->headerForm->fill($masterHeaderData);
  }

  public function edit()
  {
    $halaman = 'tr_pemesanan_penjualan-edit';
    $halamanId = \App\Models\HakAkses::where('nama', $halaman)->value('id');

    \Illuminate\Support\Facades\Gate::authorize('edit', [
      \App\Models\HakAkses::class,
      $halamanId,
    ]);


    $this->optionStatus = $this->aksesStatusOption();

    $this->isReadonly = false;
    $this->isDisabled = false;
    $masterHeaderData = $this->headerModel::findOrFail($this->id);
    $this->headerForm->fill($masterHeaderData);
  }

  public function update()
  {
    $halaman = 'tr_pemesanan_penjualan-update';

    $validatedHeaderForm = $this->validate(
      $this->headerForm->rules(),
      [],
      $this->headerForm->attributes()
    )['headerForm'];
    $msCabangId = $validatedHeaderForm['ms_cabang_id'];
    $statusId = $validatedHeaderForm['status_option'];
    $halamanId = \App\Models\HakAkses::where('nama', $halaman)->value('id');

    \Illuminate\Support\Facades\Gate::authorize('update', [
      \App\Models\HakAkses::class,
      $halamanId,
      $msCabangId,
      $statusId,
    ]);

    dd('stop update');

    $masterHeaderData = $this->headerModel::findOrFail($this->id);

    try {
      $validatedHeaderForm['diupdate_oleh'] = \Illuminate\Support\Facades\Auth::guard('pegawai')->user()->nama ?? null;
      $masterHeaderData->update($validatedHeaderForm);
      $this->redirect('/pemesanan-penjualan', true);
      $this->success('Data berhasil di update');
    } catch (\Throwable $e) {
      \Log::error('Data failed : ' . $e->getMessage());

      \Illuminate\Support\Facades\DB::rollBack();
      $this->error('Data gagal di update');
    }
  }



  public array $sortBy = ['column' => 'nama', 'direction' => 'desc'];

  #[Computed]
  public function headers(): array
  {
    return [
      ['key' => 'action', 'label' => 'Action', 'sortable' => false, 'class' => 'whitespace-nowrap border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'id', 'label' => 'ID', 'sortBy' => 'id', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'ms_barang_id', 'label' => 'Barang ID', 'sortBy' => 'ms_barang_id', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-left'],
      ['key' => 'catatan', 'label' => 'Catatan', 'sortBy' => 'catatan', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'qty', 'label' => 'Quantity', 'sortBy' => 'qty', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'status', 'label' => 'Status', 'sortBy' => 'status', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center'],
      ['key' => 'tgl_dibuat', 'label' => 'Created At', 'format' => ['date', 'Y-m-d H:i:s'], 'sortBy' => 'tgl_dibuat', 'class' => 'whitespace-nowrap  border-1 border-l-1 border-gray-300 dark:border-gray-600 text-center']
    ];
  }

  #[Computed]
  public function rows(): LengthAwarePaginator
  {

    $query = TrPemesananPenjualanDetail::query();
    $query->when($this->search, fn($q) => $q->where('id', 'like', "%{$this->search}%"))
      ->when(($this->filters['id'] ?? ''), fn($q) => $q->where('id', 'like', "%{$this->filters['id']}%"))
      ->when(($this->filters['ms_barang_id'] ?? ''), fn($q) => $q->where('ms_barang_id', 'like', "%{$this->filters['ms_barang_id']}%"))
      ->when(($this->filters['catatan'] ?? ''), fn($q) => $q->where('catatan', 'like', "%{$this->filters['catatan']}%"))
      ->when(($this->filters['qty'] ?? ''), fn($q) => $q->where('qty', $this->filters['qty']))
      ->when(($this->filters['tgl_dibuat'] ?? ''), function ($q) {
        $dateTime = $this->filters['tgl_dibuat'];
        $dateOnly = substr($dateTime, 0, 10);
        $q->whereDate('tgl_dibuat', $dateOnly);
      });

    $paginator = $query
      ->paginate(20);

    $start = ($paginator->currentPage() - 1) * $paginator->perPage();

    $paginator->getCollection()->transform(function ($item, $key) use ($start) {
      return $item;
    });

    return $paginator;
  }

  public function cariPelanggan(string $value = '')
  {
    $selectedOption = \App\Models\MsPelanggan::where('id', $this->headerForm->ms_pelanggan_id)->get();
    $this->pencarianPelanggan = \App\Models\MsPelanggan::query()
      ->where('nama', 'like', "%$value%")
      ->orderBy('tgl_dibuat')
      ->get()
      ->merge($selectedOption);

    $this->pencarianPelanggan = $this->pencarianPelanggan->map(function ($pelanggan) {
      return [
        'id' => $pelanggan->id,
        'name' => $pelanggan->nama,
      ];
    });
  }

  public function cariCabang(string $value = '')
  {

    $selectedOption = \App\Models\MsCabang::where('id', $this->headerForm->ms_cabang_id)->get();
    $this->pencarianCabang = \App\Models\MsCabang::query()
      ->where('nama', 'like', "%$value%")
      ->orderBy('tgl_dibuat')
      ->get()
      ->merge($selectedOption);

    $this->pencarianCabang = $this->pencarianCabang->map(function ($cabang) {
      return [
        'id' => $cabang->id,
        'name' => $cabang->nama,
      ];
    });
  }


  public function cariBarang(string $value = '')
  {

    $selectedOption = \App\Models\MsBarang::where('id', $this->detailForm->ms_barang_id)->get();
    $this->pencarianBarang = \App\Models\MsBarang::query()
      ->where('nama', 'like', "%$value%")
      ->orderBy('tgl_dibuat')
      ->get()
      ->merge($selectedOption);

    $this->pencarianBarang = $this->pencarianBarang->map(function ($barang) {
      return [
        'id' => $barang->id,
        'name' => $barang->nama,
      ];
    });
  }


  public function hapus()
  {
    $masterHeaderData = TrPemesananPenjualanHeader::findOrFail($this->id);
    $masterHeaderData->update(['status' => 'tidak-aktif']);
  }
}
