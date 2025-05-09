<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.navigation')

        <body class="flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <div class="main-container">
            <h1 class="main-title">Собери свой идеальный ПК</h1>
            <p class="main-subtitle">Выберите лучшие комплектующие и создайте мощный ПК! Для того, чтобы это сделать необходимо авторизоваться!</p>
            <a href="{{ url('/configurator') }}" class="btn-start">Начать сборку</a>
            
        </div>
    </body>
    <!--<footer class="bg-gray-900" aria-labelledby="footer-heading">
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="mx-auto max-w-7xl px-6 py-16 sm:py-24 lg:px-8 lg:py-32">
          <div class="xl:grid xl:grid-cols-3 xl:gap-8">
            <img class="h-7" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="MyConfigurator">
            <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
              <div class="md:grid md:grid-cols-2 md:gap-8">
                <div>
                  <h3 class="text-sm font-semibold leading-6">Solutions</h3>
                  <ul role="list" class="mt-6 space-y-4">
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Marketing</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Analytics</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Commerce</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Insights</a>
                    </li>
                  </ul>
                </div>
                <div class="mt-10 md:mt-0">
                  <h3 class="text-sm font-semibold leading-6">Support</h3>
                  <ul role="list" class="mt-6 space-y-4">
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Pricing</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Documentation</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Guides</a>
                    </li>
      
                    
                  </ul>
                </div>
              </div>
              <div class="md:grid md:grid-cols-2 md:gap-8">
                <div>
                  <h3 class="text-sm font-semibold leading-6">Компания</h3>
                  <ul role="list" class="mt-6 space-y-4">
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">О нас</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Блог</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Jobs</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Press</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Partners</a>
                    </li>
                  </ul>
                </div>
                <div class="mt-10 md:mt-0">
                  <h3 class="text-sm font-semibold leading-6>Legal</h3>
                  <ul role="list" class="mt-6 space-y-4">
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Claim</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Privacy</a>
                    </li>
      
                    <li>
                      <a href="#" class="text-sm leading-6 hover:text-white">Terms</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>-->
</html>
