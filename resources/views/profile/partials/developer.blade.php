<section class="overflow-hidden ">
    <div class="relative mx-auto max-w-7xl px-6 pt-20 pb-12 lg:px-8 lg:py-20">
      <svg class="absolute top-full left-0 translate-x-80 -translate-y-24 transform lg:hidden" width="784" height="404" fill="none" viewBox="0 0 784 404" aria-hidden="true">
        <defs>
          <pattern id="e56e3f81-d9c1-4b83-a3ba-0d0ac8c32f32" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
            <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
          </pattern>
        </defs>
        <rect width="784" height="404" fill="url(#e56e3f81-d9c1-4b83-a3ba-0d0ac8c32f32)" />
      </svg>
  
      <svg class="absolute right-full top-1/2 hidden translate-x-1/2 -translate-y-1/2 transform lg:block" width="404" height="784" fill="none" viewBox="0 0 404 784" aria-hidden="true">
        <rect width="404" height="784" fill="url(#56409614-3d62-4985-9a10-7ca758a8f4f0)" />
      </svg>
      @php
      $imageUrl = 
          asset('storage/products/developer.jpg');
      @endphp
  
      <div class="relative lg:flex lg:items-center">
        <div class="hidden lg:block lg:flex-shrink-0">
          <img class="h-64 w-64 rounded-full xl:h-80 xl:w-80" src="{{ $imageUrl }}" alt="">
        </div>
  
        <div class="relative lg:ml-10">
          <svg class="absolute top-0 left-0 h-18 w-18 -translate-x-8 -translate-y-24 transform text-indigo-200 opacity-50" stroke="currentColor" fill="none" viewBox="0 0 144 144" aria-hidden="true">
            <path stroke-width="2" d="M41.485 15C17.753 31.753 1 59.208 1 89.455c0 24.664 14.891 39.09 32.109 39.09 16.287 0 28.386-13.03 28.386-28.387 0-15.356-10.703-26.524-24.663-26.524-2.792 0-6.515.465-7.446.93 2.327-15.821 17.218-34.435 32.11-43.742L41.485 15zm80.04 0c-23.268 16.753-40.02 44.208-40.02 74.455 0 24.664 14.891 39.09 32.109 39.09 15.822 0 28.386-13.03 28.386-28.387 0-15.356-11.168-26.524-25.129-26.524-2.792 0-6.049.465-6.98.93 2.327-15.821 16.753-34.435 31.644-43.742L121.525 15z" />
          </svg>
          <div class="max-w-xl mx-auto text-center mt-10">
            <h2 class="text-3xl font-bold mb-4">Адамян Тигран — Разработчик</h2>
            <blockquote class="relative p-6 rounded-2xl shadow-md">
              <div class="text-2xl font-medium leading-9">
                <p>Идеальный компьютер — не тот, что купил, а тот, что собрал сам.</p>
              </div>
            </blockquote>
          </div>
          
        </div>
      </div>
    </div>
  </section>
  