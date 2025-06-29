<div>
  <x-list-menu :title="$title" :url="$url" shadow />

  <div class="bg-cream text-charcoal min-h-screen font-sans leading-normal overflow-x-hidden lg:overflow-auto">
    <div class="flex-1 md:p-0 lg:pt-8 lg:px-8 md:ml-24 flex flex-col">
      <section class="bg-cream-lighter p-4 shadow">
        <div class="md:flex">
          <h2 class="md:w-1/3 uppercase tracking-wide text-sm sm:text-lg mb-6">INFORMASI PEGAWAI</h2>
        </div>
        <form>
          <div class="md:flex mb-8">
            <div class="md:w-1/3">
              <legend class="uppercase tracking-wide text-sm">Location</legend>
              <p class="text-xs font-light text-red">This entire section is required.</p>
            </div>
            <div class="md:flex-1 mt-2 mb:mt-0 md:px-3">
              <div class="mb-4">
                <label class="block uppercase tracking-wide text-xs font-bold">Name</label>
                <input class="w-full shadow-inner p-4 border-0" type="text" name="name"
                  placeholder="Acme Mfg. Co.">
              </div>
              <div class="md:flex mb-4">
                <div class="md:flex-1 md:pr-3">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Street
                    Address</label>
                  <input class="w-full shadow-inner p-4 border-0" type="text" name="address_street"
                    placeholder="555 Roadrunner Lane">
                </div>
                <div class="md:flex-1 md:pl-3">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Building/Suite
                    No.</label>
                  <input class="w-full shadow-inner p-4 border-0" type="text" name="address_number" placeholder="#3">
                  <span class="text-xs mb-4 font-thin">We lied, this isn't required.</span>
                </div>
              </div>
              <div class="md:flex mb-4">
                <div class="md:flex-1 md:pr-3">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Latitude</label>
                  <input class="w-full shadow-inner p-4 border-0" type="text" name="lat"
                    placeholder="30.0455542">
                </div>
                <div class="md:flex-1 md:pl-3">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Longitude</label>
                  <input class="w-full shadow-inner p-4 border-0" type="text" name="lon"
                    placeholder="-99.1405168">
                </div>
              </div>
            </div>
          </div>
          <div class="md:flex mb-8">
            <div class="md:w-1/3">
              <legend class="uppercase tracking-wide text-sm">Contact</legend>
            </div>
            <div class="md:flex-1 mt-2 mb:mt-0 md:px-3">
              <div class="mb-4">
                <label class="block uppercase tracking-wide text-xs font-bold">Phone</label>
                <input class="w-full shadow-inner p-4 border-0" type="tel" name="phone"
                  placeholder="(555) 555-5555">
              </div>
              <div class="mb-4">
                <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">URL</label>
                <input class="w-full shadow-inner p-4 border-0" type="url" name="url" placeholder="acme.co">
              </div>
              <div class="mb-4">
                <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Email</label>
                <input class="w-full shadow-inner p-4 border-0" type="email" name="email"
                  placeholder="contact@acme.co">
              </div>
            </div>
          </div>
          <div class="md:flex">
            <div class="md:w-1/3">
              <legend class="uppercase tracking-wide text-sm">Social</legend>
            </div>
            <div class="md:flex-1 mt-2 mb:mt-0 md:px-3">
              <div class="md:flex mb-4">
                <div class="md:flex-1 md:pr-3">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Facebook</label>
                  <div class="w-full flex">
                    <span class="text-xs py-4 px-2 bg-grey-light text-grey-dark">facebook.com/</span>
                    <input class="flex-1 shadow-inner p-4 border-0" type="text" name="facebook" placeholder="acmeco">
                  </div>
                </div>
                <div class="md:flex-1 md:pl-3 mt-2 md:mt-0">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Twitter</label>
                  <div class="w-full flex">
                    <span class="text-xs py-4 px-2 bg-grey-light text-grey-dark">twitter.com/</span>
                    <input class="flex-1 shadow-inner p-4 border-0" type="text" name="twitter" placeholder="acmeco">
                  </div>
                </div>
              </div>
              <div class="md:flex mb-4">
                <div class="md:flex-1 md:pr-3">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Instagram</label>
                  <div class="w-full flex">
                    <span class="text-xs py-4 px-2 bg-grey-light text-grey-dark">instagram.com/</span>
                    <input class="flex-1 shadow-inner p-4 border-0" type="text" name="instagram"
                      placeholder="acmeco">
                  </div>
                </div>
                <div class="md:flex-1 md:pl-3 mt-2 md:mt-0">
                  <label class="block uppercase tracking-wide text-charcoal-darker text-xs font-bold">Yelp</label>
                  <div class="w-full flex">
                    <span class="text-xs py-4 px-2 bg-grey-light text-grey-dark">yelp.com/</span>
                    <input class="flex-1 shadow-inner p-4 border-0" type="text" name="yelp"
                      placeholder="acmeco">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="md:flex mb-6">
            <div class="md:w-1/3">
              <legend class="uppercase tracking-wide text-sm">Description</legend>
            </div>
            <div class="md:flex-1 mt-2 mb:mt-0 md:px-3">
              <textarea class="w-full shadow-inner p-4 border-0" placeholder="We build fine acmes." rows="6"></textarea>
            </div>
          </div>
          <div class="md:flex mb-6">
            <div class="md:w-1/3">
              <legend class="uppercase tracking-wide text-sm">Cover Image</legend>
            </div>
            <div class="md:flex-1 px-3 text-center">
              <div class="button bg-gold hover:bg-gold-dark text-cream mx-auto cusor-pointer relative">
                <input class="opacity-0 absolute pin-x pin-y" type="file" name="cover_image">
                Add Cover Image
              </div>
            </div>
          </div>
          <div class="md:flex mb-6 border border-t-1 border-b-0 border-x-0 border-cream-dark">
            <div class="md:flex-1 px-3 text-center md:text-right">
              <input type="hidden" name="sponsor" value="0">
              <input class="button text-cream-lighter bg-brick hover:bg-brick-dark" type="submit"
                value="Create Location">
            </div>
          </div>
        </form>
      </section>
    </div>
  </div>

</div>
