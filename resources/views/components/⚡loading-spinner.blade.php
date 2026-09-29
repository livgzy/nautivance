<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div
    wire:loading
    wire:target="save, update, delete"
    class="loading-overlay"
>
    <div class="loadingio-spinner-double-ring">
        <div class="ldio-double-ring">
            <div></div>
            <div></div>
            <div><div></div></div>
            <div><div></div></div>
        </div>
    </div>
</div>