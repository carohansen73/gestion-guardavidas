{{-- <form>

    <div class="space-y-12  ">
        <div class="border-b border-gray-900/10 pb-12 dark:border-white/10  rounded-lg bg-white dark:bg-gray-600  px-4 py-10">
            <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Intervención</h2>
            <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Por favor complete los campos a continuación para registrar una intervención.</p>

            <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-4">
                <label for="username" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Username</label>
                <div class="mt-2">
                    <div class="flex items-center rounded-md bg-white pl-3 outline-1 -outline-offset-1 outline-gray-300 focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-600 dark:bg-white/5 dark:outline-white/10 dark:focus-within:outline-indigo-500">
                    <div class="shrink-0 text-base text-gray-500 select-none sm:text-sm/6 dark:text-gray-400">workcation.com/</div>
                    <input id="fecha" type="date" name="fecha" placeholder="fecha" class="block min-w-0 grow bg-white py-1.5 pr-3 pl-1 text-base text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6 dark:bg-transparent dark:text-white dark:placeholder:text-gray-500" />
                    </div>
                </div>
                </div>

                <div class="col-span-full">
                    <label for="about" class="block text-sm/6 font-medium text-gray-900 dark:text-white">About</label>
                    <div class="mt-2">
                        <textarea id="about" name="about" rows="3" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500"></textarea>
                    </div>
                    <p class="mt-3 text-sm/6 text-gray-600 dark:text-gray-400">Write a few sentences about yourself.</p>
                </div>

                <div class="col-span-full">
                    <label for="photo" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Photo</label>
                    <div class="mt-2 flex items-center gap-x-3">
                        <svg viewBox="0 0 24 24" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-12 text-gray-300 dark:text-gray-500">
                        <path d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        <button type="button" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">Change</button>
                    </div>
                </div>

            <div class="col-span-full">
                <label for="cover-photo" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Cover photo</label>
                <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10 dark:border-white/25">
                    <div class="text-center">
                        <svg viewBox="0 0 24 24" fill="currentColor" data-slot="icon" aria-hidden="true" class="mx-auto size-12 text-gray-300 dark:text-gray-600">
                            <path d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" fill-rule="evenodd" />
                        </svg>
                        <div class="mt-4 flex text-sm/6 text-gray-600 dark:text-gray-400">
                            <label for="file-upload" class="relative cursor-pointer rounded-md bg-transparent font-semibold text-indigo-600 focus-within:outline-2 focus-within:outline-offset-2 focus-within:outline-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:focus-within:outline-indigo-500 dark:hover:text-indigo-300">
                            <span>Upload a file</span>
                            <input id="file-upload" type="file" name="file-upload" class="sr-only" />
                            </label>
                            <p class="pl-1">or drag and drop</p>
                        </div>
                        <p class="text-xs/5 text-gray-600 dark:text-gray-400">PNG, JPG, GIF up to 10MB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="border-b border-gray-900/10 pb-12 dark:border-white/10">
      <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Personal Information</h2>
      <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">Use a permanent address where you can receive mail.</p>

      <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
        <div class="sm:col-span-3">
          <label for="first-name" class="block text-sm/6 font-medium text-gray-900 dark:text-white">First name</label>
          <div class="mt-2">
            <input id="first-name" type="text" name="first-name" autocomplete="given-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
          </div>
        </div>

        <div class="sm:col-span-3">
          <label for="last-name" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Last name</label>
          <div class="mt-2">
            <input id="last-name" type="text" name="last-name" autocomplete="family-name" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
          </div>
        </div>

        <div class="sm:col-span-4">
          <label for="email" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Email address</label>
          <div class="mt-2">
            <input id="email" type="email" name="email" autocomplete="email" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
          </div>
        </div>

        <div class="sm:col-span-3">
          <label for="country" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Country</label>
          <div class="mt-2 grid grid-cols-1">
            <select id="country" name="country" autocomplete="country-name" class="col-start-1 row-start-1 w-full appearance-none rounded-md bg-white py-1.5 pr-8 pl-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:*:bg-gray-800 dark:focus:outline-indigo-500">
              <option>United States</option>
              <option>Canada</option>
              <option>Mexico</option>
            </select>
            <svg viewBox="0 0 16 16" fill="currentColor" data-slot="icon" aria-hidden="true" class="pointer-events-none col-start-1 row-start-1 mr-2 size-5 self-center justify-self-end text-gray-500 sm:size-4 dark:text-gray-400">
              <path d="M4.22 6.22a.75.75 0 0 1 1.06 0L8 8.94l2.72-2.72a.75.75 0 1 1 1.06 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L4.22 7.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
            </svg>
          </div>
        </div>

        <div class="col-span-full">
          <label for="street-address" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Street address</label>
          <div class="mt-2">
            <input id="street-address" type="text" name="street-address" autocomplete="street-address" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
          </div>
        </div>

        <div class="sm:col-span-2 sm:col-start-1">
          <label for="city" class="block text-sm/6 font-medium text-gray-900 dark:text-white">City</label>
          <div class="mt-2">
            <input id="city" type="text" name="city" autocomplete="address-level2" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
          </div>
        </div>

        <div class="sm:col-span-2">
          <label for="region" class="block text-sm/6 font-medium text-gray-900 dark:text-white">State / Province</label>
          <div class="mt-2">
            <input id="region" type="text" name="region" autocomplete="address-level1" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
          </div>
        </div>

        <div class="sm:col-span-2">
          <label for="postal-code" class="block text-sm/6 font-medium text-gray-900 dark:text-white">ZIP / Postal code</label>
          <div class="mt-2">
            <input id="postal-code" type="text" name="postal-code" autocomplete="postal-code" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6 dark:bg-white/5 dark:text-white dark:outline-white/10 dark:placeholder:text-gray-500 dark:focus:outline-indigo-500" />
          </div>
        </div>
      </div>
    </div>

    <div class="border-b border-gray-900/10 pb-12 dark:border-white/10">
      <h2 class="text-base/7 font-semibold text-gray-900 dark:text-white">Notifications</h2>
      <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">We'll always let you know about important changes, but you pick what else you want to hear about.</p>

      <div class="mt-10 space-y-10">
        <fieldset>
          <legend class="text-sm/6 font-semibold text-gray-900 dark:text-white">By email</legend>
          <div class="mt-6 space-y-6">
            <div class="flex gap-3">
              <div class="flex h-6 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input id="comments" type="checkbox" name="comments" checked aria-describedby="comments-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto" />
                  <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                    <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                    <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                  </svg>
                </div>
              </div>
              <div class="text-sm/6">
                <label for="comments" class="font-medium text-gray-900 dark:text-white">Comments</label>
                <p id="comments-description" class="text-gray-500 dark:text-gray-400">Get notified when someones posts a comment on a posting.</p>
              </div>
            </div>
            <div class="flex gap-3">
              <div class="flex h-6 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input id="candidates" type="checkbox" name="candidates" aria-describedby="candidates-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto" />
                  <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                    <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                    <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                  </svg>
                </div>
              </div>
              <div class="text-sm/6">
                <label for="candidates" class="font-medium text-gray-900 dark:text-white">Candidates</label>
                <p id="candidates-description" class="text-gray-500 dark:text-gray-400">Get notified when a candidate applies for a job.</p>
              </div>
            </div>
            <div class="flex gap-3">
              <div class="flex h-6 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input id="offers" type="checkbox" name="offers" aria-describedby="offers-description" class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:indeterminate:border-indigo-500 dark:indeterminate:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:checked:bg-white/10 forced-colors:appearance-auto" />
                  <svg viewBox="0 0 14 14" fill="none" class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25 dark:group-has-disabled:stroke-white/25">
                    <path d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-checked:opacity-100" />
                    <path d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-0 group-has-indeterminate:opacity-100" />
                  </svg>
                </div>
              </div>
              <div class="text-sm/6">
                <label for="offers" class="font-medium text-gray-900 dark:text-white">Offers</label>
                <p id="offers-description" class="text-gray-500 dark:text-gray-400">Get notified when a candidate accepts or rejects an offer.</p>
              </div>
            </div>
          </div>
        </fieldset>

        <fieldset>
          <legend class="text-sm/6 font-semibold text-gray-900 dark:text-white">Push notifications</legend>
          <p class="mt-1 text-sm/6 text-gray-600 dark:text-gray-400">These are delivered via SMS to your mobile phone.</p>
          <div class="mt-6 space-y-6">
            <div class="flex items-center gap-x-3">
              <input id="push-everything" type="radio" name="push-notifications" checked class="relative size-4 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:before:bg-white/20 forced-colors:appearance-auto forced-colors:before:hidden" />
              <label for="push-everything" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Everything</label>
            </div>
            <div class="flex items-center gap-x-3">
              <input id="push-email" type="radio" name="push-notifications" class="relative size-4 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:before:bg-white/20 forced-colors:appearance-auto forced-colors:before:hidden" />
              <label for="push-email" class="block text-sm/6 font-medium text-gray-900 dark:text-white">Same as email</label>
            </div>
            <div class="flex items-center gap-x-3">
              <input id="push-nothing" type="radio" name="push-notifications" class="relative size-4 appearance-none rounded-full border border-gray-300 bg-white before:absolute before:inset-1 before:rounded-full before:bg-white not-checked:before:hidden checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:before:bg-gray-400 dark:border-white/10 dark:bg-white/5 dark:checked:border-indigo-500 dark:checked:bg-indigo-500 dark:focus-visible:outline-indigo-500 dark:disabled:border-white/5 dark:disabled:bg-white/10 dark:disabled:before:bg-white/20 forced-colors:appearance-auto forced-colors:before:hidden" />
              <label for="push-nothing" class="block text-sm/6 font-medium text-gray-900 dark:text-white">No push notifications</label>
            </div>
          </div>
        </fieldset>
      </div>
    </div>
  </div>

  <div class="mt-6 flex items-center justify-end gap-x-6">
    <button type="button" class="text-sm/6 font-semibold text-gray-900 dark:text-white">Cancel</button>
    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500 dark:shadow-none dark:focus-visible:outline-indigo-500">Save</button>
  </div>
</form> --}}


{{--
<form action="{{ isset($intervencion) ? route('intervencion.update', $intervencion->id) :
    route('intervencion.store') }}" method="POST" class="bg-white rounded shadow-md pb-3">
    @csrf
    {{-- @if(isset($intervencion))
        @method('PUT')
    @endif --}}

    @if ($errors->any())
    <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <div class="space-y-12">
        <div class="pb-12  px-4 py-2">

            <div class=" grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

              <!-- Playa -->
                <div class="sm:col-span-3">
                    <label for="playa_id" class="block text-sm font-medium text-gray-900 dark:text-white">Playa</label>
                    <div class="mt-2">
                        <select id="playa_id" name="playa_id"
                        class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
                            @foreach($playas as $playa)
                            <option value="{{ $playa->id }}"
                                @if( isset($intervencion) && $intervencion->playa_id == $playa->id )
                                    selected
                                @elseif(!isset($intervencion) && isset($guardavidaAuth) && $guardavidaAuth->playa_id == $playa->id)
                                    selected
                                @endif >
                                {{ $playa->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

        <!-- Puesto -->
        <div class="sm:col-span-3">
          <label for="puesto_id" class="block text-sm font-medium text-gray-900 dark:text-white">Puesto</label>
          <div class="mt-2">
            <select id="puesto_id" name="puesto_id"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
               @foreach($puestos as $puesto)
                <option value="{{ $puesto->id }}" data-playa="{{ $puesto->playa_id }}"
                    @if( isset($intervencion) && $intervencion->puesto_id == $puesto->id )
                        selected
                    @elseif(!isset($intervencion) && isset($guardavidaAuth) && $guardavidaAuth->puesto_id == $puesto->id)
                        selected
                    @endif
                >
                    {{ $puesto->nombre }}
                </option>
            @endforeach
            </select>
          </div>
        </div>

        <!-- Fecha y hora -->
        <div class="sm:col-span-2">
          <label for="fecha" class="block text-sm font-medium text-gray-900 dark:text-white">Fecha y Hora</label>
          <div class="mt-2">
            <input id="fecha" type="datetime-local" name="fecha"
              class="block w-full max-w-xs rounded-md bg-white px-2 py-1 text-sm text-gray-900
              shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 dark:bg-gray-700
              dark:text-white dark:outline-gray-500" value="{{ old('fecha', $intervencion->fecha ?? '') }}" />
          </div>
        </div>

        <!-- Tipo de intervención -->
        <div class="sm:col-span-2">
          <label for="tipo_intervencion" class="block text-sm font-medium text-gray-900 dark:text-white">Tipo de Intervención</label>
          <div class="mt-2">
            <input id="tipo_intervencion" type="text" name="tipo_intervencion" placeholder="Ej. rescate"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
              value="{{ old('tipo_intervencion', $intervencion->tipo_intervencion ?? '') }}" />
            @error('tipo_intervencion')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <!-- Víctimas -->
        <div class="sm:col-span-2">
          <label for="victimas" class="block text-sm font-medium text-gray-900 dark:text-white">Víctimas</label>
          <div class="mt-2">
            <input id="victimas" type="number" name="victimas" min="0"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
               value="{{ old('victimas', $intervencion->victimas ?? '') }}"/>
                @error('victimas')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
          </div>
        </div>


         <!-- Traslado -->
       <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-900 dark:text-white">Hubo traslado</label>
            <div class="mt-2 flex gap-x-4">
                <label class="flex items-center gap-x-2">
                <input type="radio" name="traslado" value="1"
                    {{ old('traslado', $intervencion->traslado ?? '') == '1' ? 'checked' : '' }}
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-600 border-gray-300 dark:bg-gray-700 dark:border-gray-500" />
                <span class="text-sm text-gray-700 dark:text-gray-300">Sí</span>
                </label>
                <label class="flex items-center gap-x-2">
                <input type="radio" name="traslado" value="0"
                    {{ old('traslado', $intervencion->traslado ?? '') == '0' ? 'checked' : '' }}
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-600 border-gray-300 dark:bg-gray-700 dark:border-gray-500" />
                <span class="text-sm text-gray-700 dark:text-gray-300">No</span>
                </label>
            </div>
        </div>

        <!-- Código -->
        <div class="sm:col-span-2">
          <label for="codigo" class="block text-sm font-medium text-gray-900 dark:text-white">Código</label>
          <div class="mt-2">
            <input id="codigo" type="number" name="codigo"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500"
               value="{{ old('codigo', $intervencion->codigo ?? '') }}"/>
          </div>
        </div>

        <!-- Bandera -->
        <div class="sm:col-span-2">
          <label for="bandera_id" class="block text-sm font-medium text-gray-900 dark:text-white">Bandera</label>
          <div class="mt-2">
            <select id="bandera_id" name="bandera_id"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 font-medium shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
               @foreach($banderas as $bandera)
                    <option value="{{ $bandera->id }}"
                        {{ old('bandera_id', $intervencion?->bandera_id ?? '') == $bandera->id ? 'selected' : '' }}>
                        {{ $bandera->codigo }}
                    </option>
                @endforeach
            </select>
          </div>
        </div>

        <!-- Fuerzas -->
        <div class="sm:col-span-2">
            <label for="fecha" class="block text-sm font-medium text-gray-900 dark:text-white">Intervinieron otras fuerzas</label>
            <div class="mt-2">
            <select id="fuerzas" name="fuerzas[]" multiple>
                @foreach($fuerzas as $fuerza)
                    <option value="{{ $fuerza->id }}"
                        @if(collect(old('fuerzas', $intervencion?->fuerzas->pluck('id') ?? []))->contains($fuerza->id))
                            selected
                        @endif>
                        {{ $fuerza->nombre }}
                    </option>
                @endforeach
            </select>
            </div>
        </div>

        <!-- Lista de guardavidas -->
         <div class="sm:col-span-2">
            <label for="fecha" class="block text-sm font-medium text-gray-900 dark:text-white">Guardavidas que intervinieron</label>
            <div class="mt-2">
            <select id="guardavidas" name="guardavidas[]" multiple>
                @foreach($guardavidas as $g)
                    <option value="{{ $g->id }}"
                        @if(collect(old('guardavidas', $intervencion?->guardavidas->pluck('id') ?? []))->contains($g->id))
                            selected
                        @endif>
                        {{ $g->nombre }} {{ $g->apellido }}
                    </option>
                @endforeach
            </select>
            </div>
        </div>

        <!-- Detalles -->
        <div class="col-span-full">
          <label for="detalles" class="block text-sm font-medium text-gray-900 dark:text-white">Detalles</label>
          <div class="mt-2">
            <textarea id="detalles" name="detalles" rows="4"
              class="block w-full rounded-md bg-white px-3 py-1.5 text-gray-900 shadow-sm outline outline-1 outline-gray-300 focus:outline-indigo-600 sm:text-sm dark:bg-gray-700 dark:text-white dark:outline-gray-500">
            {{ old('detalles', $intervencion->detalles ?? '') }}
        </textarea>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Botones -->
  <div class="m-6 mb-6 flex items-center justify-end gap-x-6">
    <button type="button" class="text-sm font-semibold text-gray-900 dark:text-white">Cancelar</button>
    <button type="submit"
      class="rounded-md bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 dark:bg-indigo-500">
      Guardar
    </button>
  </div>
{{-- </form> --}}

@vite(['resources/js/filterPuestoByPlaya.js'])
<!-- AlpineJS para hacerlo reactivo -->
{{-- <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}



<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">

<script>
new TomSelect("#fuerzas",{
    plugins: ['remove_button'],
    persist: false,
    create: false,
});

new TomSelect("#guardavidas",{
    plugins: ['remove_button'],
    persist: false,
    create: false,
});
</script>




