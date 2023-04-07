<p align="center">
<a href="https://github.com/wire-elements/modal/actions"><img src="https://github.com/wire-elements/modal/workflows/PHPUnit/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/wire-elements/modal"><img src="https://img.shields.io/packagist/dt/wire-elements/modal" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/livewire-ui/modal"><img src="https://img.shields.io/packagist/dt/livewire-ui/modal" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/wire-elements/modal"><img src="https://img.shields.io/packagist/v/wire-elements/modal" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/wire-elements/modal"><img src="https://img.shields.io/packagist/l/wire-elements/modal" alt="License"></a>
</p>

## About Wire Elements Modal
Wire Elements Modal is a Livewire component that provides you with a modal that supports multiple child modals while maintaining state.

## Installation

<a href="https://philo.dev/laravel-modals-with-livewire/"><img src="https://d.pr/i/GR66B3+" alt=""></a>

Click the image above to read a full article on using the Wire Elements modal package or follow the instructions below.

To get started, require the package via Composer:

```
composer require wire-elements/modal
```

## Livewire directive
Add the Livewire directive `@livewire('livewire-ui-modal')` directive to your template.
```html
<html>
<body>
    <!-- content -->

    @livewire('livewire-ui-modal')
</body>
</html>
```

## Alpine
Livewire Elements Modal requires [Alpine](https://github.com/alpinejs/alpine) and the plugin[Focus](https://alpinejs.dev/plugins/focus). You can use the official CDN to quickly include Alpine:

```html
<!-- Alpine v3 -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Focus plugin -->
<script defer src="https://unpkg.com/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
```

## TailwindCSS
The base modal is made with TailwindCSS. If you use a different CSS framework I recommend that you publish the modal template and change the markup to include the required classes for your CSS framework.
```shell
php artisan vendor:publish --tag=livewire-ui-modal-views
```


## Creating a modal
You can run `php artisan make:livewire EditUser` to make the initial Livewire component. Open your component class and make sure it extends the `ModalComponent` class:

```php
<?php

namespace App\Http\Livewire;

use LivewireUI\Modal\ModalComponent;

class EditUser extends ModalComponent
{
    public function render()
    {
        return view('livewire.edit-user');
    }
}
```

## Opening a modal
To open a modal you will need to emit an event. To open the `EditUser` modal for example:

```html
<!-- Outside of any Livewire component -->
<button onclick="Livewire.emit('openModal', 'edit-user')">Edit User</button>

<!-- Inside existing Livewire component -->
<button wire:click="$emit('openModal', 'edit-user')">Edit User</button>

<!-- Taking namespace into account for component Admin/Actions/EditUser -->
<button wire:click="$emit('openModal', 'admin.actions.edit-user')">Edit User</button>
```

## Passing parameters
To open the `EditUser` modal for a specific user we can pass the user id:

```html
<!-- Outside of any Livewire component -->
<button onclick="Livewire.emit('openModal', 'edit-user', {{ json_encode(['user' => $user->id]) }})">Edit User</button>

<!-- Inside existing Livewire component -->
<button wire:click="$emit('openModal', 'edit-user', {{ json_encode(['user' => $user->id]) }})">Edit User</button>

<!-- If you use a different primaryKey (e.g. email), adjust accordingly -->
<button wire:click="$emit('openModal', 'edit-user', {{ json_encode(['user' => $user->email]) }})">Edit User</button>

<!-- Example of passing multiple parameters -->
<button wire:click="$emit('openModal', 'edit-user', {{ json_encode([$user->id, $isAdmin]) }})">Edit User</button>
```

The parameters are injected into the modal component and the model will be automatically fetched from the database if the type is defined:

```php
<?php

namespace App\Http\Livewire;

use App\Models\User;
use LivewireUI\Modal\ModalComponent;

class EditUser extends ModalComponent
{
    // This will inject just the ID
    // public int $user;

    public User $user;

    public function mount()
    {
        Gate::authorize('update', $this->user);
    }

    public function render()
    {
        return view('livewire.edit-user');
    }
}
```

The parameters are also passed to the `mount` method on the modal component.

## Opening a child modal
From an existing modal you can use the exact same event and a child modal will be created:

```html
<!-- Edit User Modal -->

<!-- Edit Form -->

<button wire:click='$emit("openModal", "delete-user", {{ json_encode(["user" => $user->id]) }})'>Delete User</button>
```

## Closing a (child) modal
If for example a user clicks the 'Delete' button which will open a confirm dialog, you can cancel the deletion and return to the edit user modal by emitting the `closeModal` event. This will open the previous modal. If there is no previous modal the entire modal component is closed and the state will be reset.
```html
<button wire:click="$emit('closeModal')">No, do not delete</button>
```

You can also close a modal from within your modal component class:

```php
<?php

namespace App\Http\Livewire;

use App\Models\User;
use LivewireUI\Modal\ModalComponent;

class EditUser extends ModalComponent
{
    public User $user;

    public function mount()
    {
        Gate::authorize('update', $this->user);
    }

    public function update()
    {
        Gate::authorize('update', $this->user);

        $this->user->update($data);

        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.edit-user');
    }
}
```

If you don't want to go to the previous modal but close the entire modal component you can use the `forceClose` method:

```php
public function update()
{
    Gate::authorize('update', $this->user);

    $this->user->update($data);

    $this->forceClose()->closeModal();
}
```

Often you will want to update other Livewire components when changes have been made. For example, the user overview when a user is updated. You can use the `closeModalWithEvents` method to achieve this.

```php
public function update()
{
    Gate::authorize('update', $this->user);

    $this->user->update($data);

    $this->closeModalWithEvents([
        UserOverview::getName() => 'userModified',
    ]);
}
```

It's also possible to add parameters to your events:

```php
public function update()
{
    $this->user->update($data);

    $this->closeModalWithEvents([
        UserOverview::getName() => ['userModified', [$this->user->id]],
    ]);
}
```

## Changing modal properties

You can change the width (default value '2xl') of the modal by overriding the static `modalMaxWidth` method in your modal component class:

```php
/**
 * Supported: 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
 */
public static function modalMaxWidth(): string
{
    return 'xl';
}
```

By default, the modal will close when you hit the `escape` key. If you want to disable this behavior to, for example, prevent accidentally closing the modal you can overwrite the static `closeModalOnEscape` method and have it return `false`.
```php
public static function closeModalOnEscape(): bool
{
    return false;
}
```

By default, the modal will close when you click outside the modal. If you want to disable this behavior to, for example, prevent accidentally closing the modal you can overwrite the static `closeModalOnClickAway` method and have it return `false`.
 ```php
 public static function closeModalOnClickAway(): bool
 {
     return false;
 }
 ```

 By default, closing a modal by pressing the escape key will force close all modals. If you want to disable this behavior to, for example, allow pressing escape to show a previous modal, you can overwrite the static `closeModalOnEscapeIsForceful` method and have it return `false`.
 ```php
 public static function closeModalOnEscapeIsForceful(): bool
 {
     return false;
 }
 ```

 When a modal is closed, you can optionally enable a `modalClosed` event to be fired. This event will be fired on a call to `closeModal`, when the escape button is pressed, or when you click outside the modal. The name of the closed component will be provided as a parameter;
 ```php
 public static function dispatchCloseEvent(): bool
 {
     return true;
 }
 ```

 By default, when a child modal is closed, the closed components state is still available if the same modal component is opened again. If you would like to destroy the component when its closed you can override the static `destroyOnClose` method and have it return `true`. When a destroyed modal is opened again its state will be reset.
 ```php
 public static function destroyOnClose(): bool
 {
     return true;
 }
 ```

## Skipping previous modals
In some cases you might want to skip previous modals. For example:
1. Team overview modal
2. -> Edit Team
3. -> Delete Team

In this case, when a team is deleted, you don't want to go back to step 2 but go back to the overview.
You can use the `skipPreviousModal` method to achieve this. By default it will skip the previous modal. If you want to skip more you can pass the number of modals to skip `skipPreviousModals(2)`.

```php
<?php

namespace App\Http\Livewire;

use App\Models\Team;
use LivewireUI\Modal\ModalComponent;

class DeleteTeam extends ModalComponent
{
    public Team $team;

    public function mount(Team $team)
    {
        $this->team = $team;
    }

    public function delete()
    {
        Gate::authorize('delete', $this->team);

        $this->team->delete();

        $this->skipPreviousModal()->closeModalWithEvents([
            TeamOverview::getName() => 'teamDeleted'
        ]);
    }

    public function render()
    {
        return view('livewire.delete-team');
    }
}
```

You can also optionally call the `destroySkippedModals()` method to destroy the skipped modals so if any are opened again their state will be reset





## Building Tailwind CSS for production
To purge the classes used by the package, add the following lines to your purge array in `tailwind.config.js`:
```js
'./vendor/wire-elements/modal/resources/views/*.blade.php',
'./storage/framework/views/*.php',
```

Because some classes are dynamically build you should add some classes to the purge safelist so your `tailwind.config.js` should look something like this:
```js
module.exports = {
  purge: {
    content: [
      './vendor/wire-elements/modal/resources/views/*.blade.php',
      './storage/framework/views/*.php',
      './resources/views/**/*.blade.php',
    ],
    options: {
      safelist: [
        'sm:max-w-2xl'
      ]
    }
  },
  darkMode: false, // or 'media' or 'class'
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
```

## Configuration
You can customize the Modal via the `livewire-ui-modal.php` config file. This includes some additional options like including CSS if you don't use TailwindCSS for your application, as well as the default modal properties.

 To publish the config run the vendor:publish command:
```shell
php artisan vendor:publish --tag=livewire-ui-modal-config
```

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Include CSS
    |--------------------------------------------------------------------------
    |
    | The modal uses TailwindCSS, if you don't use TailwindCSS you will need
    | to set this parameter to true. This includes the modern-normalize css.
    |
    */
    'include_css' => false,


    /*
    |--------------------------------------------------------------------------
    | Include JS
    |--------------------------------------------------------------------------
    |
    | Livewire UI will inject the required Javascript in your blade template.
    | If you want to bundle the required Javascript you can set this to false
    | and add `require('vendor/wire-elements/modal/resources/js/modal');`
    | to your script bundler like webpack.
    |
    */
    'include_js' => true,


    /*
    |--------------------------------------------------------------------------
    | Modal Component Defaults
    |--------------------------------------------------------------------------
    |
    | Configure the default properties for a modal component.
    |
    | Supported modal_max_width
    | 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl', '7xl'
    */
    'component_defaults' => [
        'modal_max_width' => '2xl',

        'close_modal_on_click_away' => true,

        'close_modal_on_escape' => true,

        'close_modal_on_escape_is_forceful' => true,

        'dispatch_close_event' => false,

        'destroy_on_close' => false,
    ],
];
```

## Security
If you are new to Livewire I recommend to take a look at the [security details](https://laravel-livewire.com/docs/2.x/security). In short, it's **very important** to validate all information given Livewire stores this information on the client-side, or in other words, this data can be manipulated. Like shown in the examples above, use the `Gate` facade to authorize actions.

## Credits
- [Philo Hermans](https://github.com/philoNL)
- [All Contributors](../../contributors)

## License
WireElements is open-sourced software licensed under the [MIT license](LICENSE.md).

## Beautiful components crafted with Livewire

<a href="https://wire-elements.dev/"><img src="https://philo.dev/content/images/size/w1600/2022/07/wire-elements-pro-v2.png" width="600" alt="" /></a>
