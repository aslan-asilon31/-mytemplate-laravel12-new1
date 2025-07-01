<?php

namespace App\Livewire\PemesananPenjualan;

use App\Livewire\PemesananPenjualanResources\Forms\PemesananPenjualanHeaderForm;
use Livewire\Component;
use App\Models\MsPegawai;
use App\Models\TrPemesananPenjualanHeader;
use App\Models\ProductBrand;
use App\Models\Product;
use App\Models\ProductCategoryFirst;
use App\Helpers\Permission\Traits\WithPermission;


class PemesananPenjualanDetailPerbaharui extends Component
{

  public function render()
  {
    return view('livewire.pemesanan-penjualan.pemesanan-penjualan-header-perbaharui')
      ->title($this->title);
  }

  use \Livewire\WithFileUploads;
  use \Mary\Traits\Toast;
  use WithPermission;

  #[\Livewire\Attributes\Locked]
  public string $title = 'Pemesanan Penjualan';

  public  $brands = [];

  public $selectedBrandId = '';
  public $selectedProductCategoryFirstId = '';
  public  $productCategoryFirsts = [];
  public  $productCategories = [];

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
  protected $masterModel = \App\Models\TrPemesananPenjualanHeader::class;

  public PemesananPenjualanHeaderForm $masterForm;

  public function mount()
  {

    if ($this->id && $this->readonly) {
      $this->title .= ' (Tampil)';
      $this->tampil();
    } else if ($this->id) {
      $this->title .= ' (edit)';
      $this->edit();
    } else {
      $this->title .= ' (Buat)';
      $this->buat();
    }

    $this->initialize();
  }


  public function initialize() {}



  public function store()
  {
    $validatedForm = $this->validate(
      $this->masterForm->rules(),
      [],
      $this->masterForm->attributes()
    )['masterForm'];


    \Illuminate\Support\Facades\DB::beginTransaction();
    try {

      $validatedForm['dibuat_oleh'] = \Illuminate\Support\Facades\Auth::guard('pegawai')->user()->nama;
      $validatedForm['diupdate_oleh'] = \Illuminate\Support\Facades\Auth::guard('pegawai')->user()->nama;
      $validatedForm['is_activated'] = 1;
      // image_url
      $folderName = $this->baseFolderName;
      $now = now()->format('Ymd_His_u');
      $imageName = $this->baseImageName . '_' . str($validatedForm['name'])->slug('_')  . '_' . 'image' . '_' . $now;
      $newImageUrl = $validatedForm['image_url'];

      $validatedForm['image_url'] = $this->saveImage(
        $folderName,
        $imageName,
        $newImageUrl,
      );
      // ./image_url

      $this->masterModel::create($validatedForm);
      \Illuminate\Support\Facades\DB::commit();
      $this->redirect('/products', true);
      $this->success('Data has been stored');
    } catch (\Throwable $th) {
      \Illuminate\Support\Facades\DB::rollBack();
      \Log::error('Data product failed to store: ' . $th->getMessage());

      $this->error('Data failed to store');
    }
  }

  public function buat()
  {
    $this->permission('tr_pemesanan_penjualan-buat');
    $this->masterForm->reset();
    // dd('cek1');

    $nomorTerakhir = \Illuminate\Support\Facades\DB::table('ms_barang')->max('nomor') ?? 0;
    $this->masterForm->nomor = $nomorTerakhir + 1;
  }

  public function tampil()
  {
    $this->isReadonly = true;
    $this->isDisabled = true;
    $masterData = $this->masterModel::findOrFail($this->id);
    $this->masterForm->fill($masterData);
  }

  public function edit()
  {

    $this->permission('tr_pemesanan_penjualan-edit');

    $this->isReadonly = false;
    $this->isDisabled = false;
    $masterData = $this->masterModel::findOrFail($this->id);
    $this->masterForm->fill($masterData);
  }

  public function update()
  {
    $validatedForm = $this->validate(
      $this->masterForm->rules(),
      [],
      $this->masterForm->attributes()
    )['masterForm'];
    $masterData = $this->masterModel::findOrFail($this->id);

    try {

      $validatedForm['diupdate_oleh'] = \Illuminate\Support\Facades\Auth::guard('pegawai')->user()->nama ?? null;

      // image_url
      $folderName = $this->baseFolderName;
      $now = now()->format('Ymd_His_u');
      $imageName = $this->baseImageName . '_' . str($validatedForm['name'])->slug('_')  . '_' . 'image' . '_' . $now;
      $newImageUrl = $validatedForm['image_url'];

      $validatedForm['image_url'] = $this->saveImage(
        $folderName,
        $imageName,
        $newImageUrl,
      );
      // ./image_url

      $masterData->update($validatedForm);
      $this->redirect('/products', true);


      $this->success('Data has been updated');
    } catch (\Throwable $e) {
      \Log::error('Data failed : ' . $e->getMessage());

      \Illuminate\Support\Facades\DB::rollBack();
      $this->error('Data failed to update');
    }
  }

  public function delete()
  {
    $masterData = $this->masterModel::findOrFail($this->id);

    \Illuminate\Support\Facades\DB::beginTransaction();
    try {

      $this->deleteImage($masterData['image_url']);

      $masterData->delete();
      \Illuminate\Support\Facades\DB::commit();
      $this->redirect('/products', true);

      $this->success('Data has been deleted');
    } catch (\Throwable $th) {
      \Illuminate\Support\Facades\DB::rollBack();
      $this->error('Data failed to delete');
    }
  }
}
