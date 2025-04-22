<?php

use Illuminate\Contracts\Filesystem\Filesystem;

function tmp(): Filesystem
{
    return Storage::disk("tmp");
}