<?php

namespace App\Traits;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HasMedia
{
    use InteractsWithMedia;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('uploads')
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png'])
            ->useFallbackUrl(url('/imgs/no-image-100x100.svg'));

        $this->addMediaCollection('thumbnail')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpg', 'image/jpeg', 'image/png'])
            ->useFallbackUrl(url('/imgs/no-image-100x100.svg'));
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $conversions = [
            'large' => [
                'width' => 800,
                'height' => 800,
            ],
            'medium' => [
                'width' => 500,
                'height' => 500,
            ],
        ];

        foreach ($conversions as $key => $conversion) {
            $this->addMediaConversion($key)
                ->fit(
                    Fit::Fill,
                    $conversion['width'],
                    $conversion['height']
                )->keepOriginalImageFormat();
        }
    }
}
