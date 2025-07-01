<?php

namespace App\Livewire\PemesananPenjualan\Forms;

use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form;

class PemesananPenjualanHeaderForm extends Form
{
  public string|null $nama = null;
  public string|null $ms_pelanggan_id = null;
  public string|null $ms_cabang_id = null;
  public string|null $memo = null;
  public string|null $status = "";
  public string|null $status_option = "";

  public function rules(string|null $id = null): array
  {
    return [
      'headerForm.nama' => 'required|string',
      'headerForm.ms_pelanggan_id' => 'required|string',
      'headerForm.ms_cabang_id' => 'required|string',
      'headerForm.memo' => 'required|string',
      'headerForm.status' => 'required|string',
      'headerForm.status_option' => 'required|string',
    ];
  }

  public function attributes(): array
  {
    return [
      'headerForm.nama' => 'Nama',
      'headerForm.ms_pelanggan_id' => 'Pelanggan ID',
      'headerForm.ms_cabang_id' => 'Cabang ID',
      'headerForm.status' => 'Status',
      'headerForm.status_option' => 'Status Option',
    ];
  }
}
