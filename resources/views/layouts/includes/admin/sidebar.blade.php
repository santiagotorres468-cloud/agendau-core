@php
   $links = [ 
      [
         'name' => 'Dashboard',
         'icon' => 'fa-solid fa-sliders',
         'route' => route('admin.dashboard'),
         'active' => request()->routeIs('admin.dashboard'),
      ],
      [
         'header' => 'Administrar página' 
      ],
      [
         'name' => 'Usuarios',
         'icon' => 'fa-solid fa-users',
         'route' => '#', 
         'active' => false,
      ],
      [
         'name' => 'Empresa',
         'icon' => 'fa-solid fa-building',
         'active' => false,
         'submenu' => [
            [
               'name'=> 'Información',
               'icon'=> 'fa-regular fa-circle',
               'route' => '#', 
               'active'=> false,
            ],
            [
               'name'=> 'Información',
               'icon'=> 'fa-regular fa-circle',
               'route' => '#', 
               'active'=> false,
            ]
         ]
      ]
   ]; 
@endphp

<aside id="sidebar"
   class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full sm:translate-x-0 bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700"
   :class="{ 'translate-x-0': open, '-translate-x-full': !open }"
   aria-label="Sidebar">

   <div class="h-full px-3 py-4 overflow-y-auto">
      <ul class="space-y-2 font-medium">
         
         @foreach ($links as $link)
            
            @if(isset($link['header']))
               <li>
                  <div class="px-3 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase">
                     {{ $link['header'] }}
                  </div>
               </li>

            @elseif(isset($link['submenu']))
               <li x-data="{ openMenu: {{ $link['active'] ? 'true' : 'false' }} }">
                  <button @click="openMenu = !openMenu" 
                     class="flex items-center w-full px-2 py-1.5 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ $link['active'] ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                     <span class="inline-flex w-6 h-6 justify-center items-center">
                        <i class="{{ $link['icon'] }}"></i>
                     </span>
                     <span class="ms-3 flex-1 text-left">{{ $link['name'] }}</span>
                     <i class="fa-solid fa-chevron-down text-sm transition-transform duration-200" :class="{ 'rotate-180': openMenu }"></i>
                  </button>
                  
                  <ul x-show="openMenu" x-cloak x-transition class="py-2 space-y-2 pl-8">
                     @foreach ($link['submenu'] as $sublink)
                        <li>
                           <a href="{{ $sublink['route'] }}"
                              class="flex items-center px-2 py-1.5 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ $sublink['active'] ? 'text-blue-600 dark:text-blue-500 font-bold' : '' }}">
                              <span class="inline-flex w-5 h-5 justify-center items-center">
                                 <i class="{{ $sublink['icon'] }}"></i>
                              </span>
                              <span class="ms-3">{{ $sublink['name'] }}</span>
                           </a>
                        </li>
                     @endforeach
                  </ul>
               </li>
              
            @else
               <li> 
                  <a href="{{ $link['route'] }}"
                     class="flex items-center px-2 py-1.5 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ $link['active'] ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                     <span class="inline-flex w-6 h-6 justify-center items-center">
                        <i class="{{ $link['icon'] }}"></i>
                     </span>
                     <span class="ms-3">{{ $link['name'] }}</span>
                  </a>
               </li>
            @endif

         @endforeach

      </ul>
   </div>
</aside>