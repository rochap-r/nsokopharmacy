---
trigger: always_on
---

## Livewire 3

### Installation & Configuration
- Install Livewire via Composer: `composer require livewire/livewire`
- Include Livewire assets using `@livewireStyles` and `@livewireScripts` in your layout
- Publish Livewire config: `php artisan livewire:publish --config`
- Use `livewire:` prefix for Livewire routes or `Route::livewire()`

### Components
- Create components with: `php artisan make:livewire ComponentName`
- Keep components focused and single-responsibility
- Use kebab-case for component names in templates
- Place components in `app/Livewire` directory
- Use `#[Layout]` attribute to specify layouts
- Use `#[Title]` attribute to set page title

### Properties
- Declare public properties for reactive data
- Use type-hinted properties for better IDE support
- Use `#[Rule]` attribute for validation
- Use `#[Locked]` to prevent property updates from frontend
- Initialize properties in `mount()` method

### Actions
- Define methods with `#[Computed]` for computed properties
- Use `#[On('event')]` to listen for events
- Use `$this->dispatch()` to emit events
- Use `$this->js()` for JavaScript execution
- Implement `mount()` for component initialization

### Templates
- Use `livewire:` directive to include components
- Prefer Alpine.js for client-side interactions
- Use `wire:model` for two-way data binding
- Use `wire:model.live` for real-time updates
- Use `wire:model.debounce.300ms` for debounced updates
- Use `wire:confirm` for confirmation dialogs

### Forms
- Use `wire:submit` for form submission
- Include `@csrf` in forms
- Use `wire:model` for form inputs
- Implement form objects for complex forms
- Use `$this->reset()` to reset form fields

### Lifecycle Hooks
- `mount()` - Initialize component
- `hydrate()` - After hydration
- `updating()` - Before property update
- `updated()` - After property update
- `updating[PropertyName]` - Before specific property update
- `updated[PropertyName]` - After specific property update
- `render()` - Render component view

### Events
- Use `$this->dispatch()` to emit events
- Use `$this->dispatchTo()` to target specific components
- Use `$this->dispatchBrowserEvent()` for JavaScript events
- Use `$this->dispatchSelf()` for self-emitted events
- Use `$this->dispatchToOthers()` for broadcasting to other users

### File Uploads
- Use `wire:model` with file inputs
- Add `livewire` to `content-security-policy` headers
- Use `WithFileUploads` trait
- Validate uploads with `#[Rule]` attributes
- Store uploaded files using `store()` or `storeAs()`

### Testing
- Use `Livewire::test()` for testing components
- Test component mounting with different parameters
- Test form submissions and validation
- Test emitted events
- Use `assertSet()` and `assertSee()` for assertions

### Performance
- Use `lazy` loading for heavy components
- Use `defer` for non-critical updates
- Use `wire:ignore` for third-party JavaScript
- Implement pagination for large data sets
- Use `wire:init` for lazy-loading data

### Security
- Always validate and sanitize user input
- Use `#[Rule]` attributes for validation
- Authorize actions using Laravel's authorization
- Never expose sensitive data in component properties
- Use `#[Locked]` for sensitive properties

### Best Practices
- Keep components small and focused
- Use Blade components for reusable UI elements
- Move business logic to dedicated classes
- Use services for complex operations
- Implement proper error handling
- Write tests for components

### Deployment
- Run `php artisan livewire:publish --assets` for production
- Configure Livewire's asset URL if using CDN
- Set `LIVEWIRE_ASSET_URL` in production
- Configure proper caching headers
- Monitor Livewire's performance in production

### Debugging
- Use `@livewire('component')` instead of `@livewire('component')`
- Check browser console for JavaScript errors
- Use `dd()` in component methods
- Enable Livewire's debug mode in development
- Check Laravel logs for server-side errors

### Authentication
- Use `auth` middleware for protected components
- Implement proper authorization checks
- Use Laravel's built-in authentication
- Handle session timeouts gracefully
- Implement proper error messages

### Error Handling
- Use try-catch blocks for critical operations
- Display user-friendly error messages
- Log errors appropriately
- Handle validation errors with `$this->addError()`
- Implement fallback UI for failed components
