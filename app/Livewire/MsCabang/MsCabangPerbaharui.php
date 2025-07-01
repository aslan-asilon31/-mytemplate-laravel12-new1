<?php

namespace App\Livewire\MsCabang;

use App\Livewire\MsCabang\Forms\MsCabangForm;
use Livewire\Component;


class MsCabangPerbaharui extends Component
{

  public function render()
  {
    return view('livewire.master-cabang.master-cabang-perbaharui')
      ->title($this->title);
  }

  use \Livewire\WithFileUploads;
  use \Mary\Traits\Toast;
  use \App\Helpers\FormHook\Traits\aksesOpsi;

  #[\Livewire\Attributes\Locked]
  public string $title = 'cabang';

  #[\Livewire\Attributes\Locked]
  public string $url = '/cabang';

  #[\Livewire\Attributes\Locked]
  private string $baseFolderName = '/files/images/cabang';

  #[\Livewire\Attributes\Locked]
  public string $id = '';

  #[\Livewire\Attributes\Locked]
  public string $hanyalihat = '';

  #[\Livewire\Attributes\Locked]
  public bool $isReadonly = false;

  #[\Livewire\Attributes\Locked]
  public bool $isDisabled = false;

  #[\Livewire\Attributes\Locked]
  public array $options = [];

  #[\Livewire\Attributes\Locked]
  protected $masterModel = \App\Models\MsCabang::class;

  #[\Livewire\Attributes\Locked]
  public array $optionStatus = [];

  public MsCabangForm $masterForm;

  public function mount()
  {

    if ($this->id && $this->hanyalihat) {
      $this->title .= ' (Lihat)';
      $this->lihat();
    } else if ($this->id) {
      $this->title .= ' (Edit)';
      $this->edit();
    } else {
      $this->title .= ' (Buat)';
      $this->buat();
    }

    $this->initialize();
  }

  public function initialize() {}

  public function buat()
  {
    $this->optionStatus = $this->aksesStatusOption();

    $this->masterForm->reset();
  }

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

      $this->masterModel::create($validatedForm);
      \Illuminate\Support\Facades\DB::commit();
      $this->redirect('/cabang', true);
      $this->success('Data berhasil disimpan');
    } catch (\Throwable $th) {
      \Illuminate\Support\Facades\DB::rollBack();
      \Log::error('Data gagal disimpan: ' . $th->getMessage());

      $this->error('Data gagal disimpan');
    }
  }

  public function lihat()
  {
    dd('stop');
    $this->isReadonly = true;
    $this->isDisabled = true;
    $masterData = $this->masterModel::findOrFail($this->id);
    $this->masterForm->fill($masterData);
  }

  public function edit()
  {
    $this->optionStatus = $this->aksesStatusOption();

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

      $masterData->update($validatedForm);
      $this->redirect('/cabang', true);

      $this->success('Data berhasil diupdate');
    } catch (\Throwable $e) {
      \Log::error('Data gagal diupdate : ' . $e->getMessage());

      \Illuminate\Support\Facades\DB::rollBack();
      $this->error('Data gagal di update');
    }
  }

  public function delete()
  {
    $masterData = $this->masterModel::findOrFail($this->id);

    \Illuminate\Support\Facades\DB::beginTransaction();
    try {

      $masterData->delete();
      \Illuminate\Support\Facades\DB::commit();
      $this->redirect('/cabang', true);

      $this->success('Data berhasil dihapus');
    } catch (\Throwable $th) {
      \Illuminate\Support\Facades\DB::rollBack();
      $this->error('Data gagal dihapus');
    }
  }
}
