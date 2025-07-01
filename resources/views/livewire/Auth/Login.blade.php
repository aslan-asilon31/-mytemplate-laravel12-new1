<div class="flex flex-col lg:flex-row h-screen">
  <!-- Left Pane (Image) -->
  <div class="flex items-center justify-center bg-white text-black lg:flex-1">
    <!-- Gambar untuk Mobile -->
    <div class="max-w-md text-center lg:hidden my-4">
      <img src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Welcomedd.jpg" class="w-48" alt=""
        srcset="">
    </div>

    <!-- Gambar untuk Desktop -->
    <div class="hidden lg:flex items-center justify-center flex-1">
      <img src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Welcomedd.jpg" width="524.67004" height="531.39694"
        alt="" srcset="">
    </div>
  </div>

  <!-- Right Pane (Form) -->
  <div class="w-full bg-gray-100 lg:w-1/2 flex items-center justify-center p-4">



    <x-form wire:submit="login" class="space-y-4">
      <x-input wire:model="username" label="username" placeholder="Username" icon-right="o-user" right />
      <x-password wire:model="password" class="block text-sm font-medium text-gray-700" label="password"
        placeholder="password" password-icon="o-lock-closed" password-visible-icon="o-lock-open" right />

      <x-button type="submit" spinner="login" class="block text-sm font-medium text-gray-700"
        class="bg-blue-700 hover:bg-blue-400 text-white btn-block">
        Masuk
      </x-button>
      <x-errors class="text-white" />

    </x-form>
  </div>
</div>
