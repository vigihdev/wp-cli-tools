<?php

declare(strict_types=1);

namespace Vigihdev\WpCliTools\Contracts\Able;

/**
 * Interface CompactArrayAbleInterface
 *
 * Interface untuk mendefinisikan struktur data yang dapat dikonversi ke dan dari array
 */
interface CompactArrayAbleInterface
{
    /**
     * Mengkonversi objek menjadi array
     *
     * @return array Array representasi dari objek
     */
    public function toArray(): array;

    /**
     * Membuat instance objek dari array data
     *
     * @param array $data Array data untuk membuat instance objek
     * @return static Instance objek dari tipe yang sesuai
     */
    public static function fromArray(array $data): static;
}
