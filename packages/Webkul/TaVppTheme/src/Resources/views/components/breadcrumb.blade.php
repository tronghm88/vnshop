@props([
    'name'  => '',
    'entity' => null,
])

<div class="breadcrumb">
    {{ Breadcrumbs::view('ta-vpp-theme::partials.breadcrumbs', $name, $entity) }}
</div>
