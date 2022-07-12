<?php

it('returns a collection of icons', function () {
    $icons = \App\Services\Icons::all();

    $this->assertInstanceOf(\Illuminate\Support\Collection::class, $icons);
});

it('has a few icons', function () {
    $icons = \App\Services\Icons::all();
   
    expect($icons->count())->toBeGreaterThan(0);
});
