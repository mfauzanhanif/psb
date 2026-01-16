<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WilayahService
{
    /**
     * Make HTTP request with retry logic and error handling
     */
    private static function makeRequest(string $url): ?array
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for hosting compatibility
            ])
                ->timeout(10)
                ->retry(2, 1000)
                ->get($url);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::warning("WilayahService: API request failed", [
                'url' => $url,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("WilayahService: API request exception", [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public static function getProvinces(): array
    {
        return Cache::remember('api_provinces', 3600, function () {
            $data = self::makeRequest('https://wilayah.id/api/provinces.json');
            if ($data) {
                return collect($data)->pluck('name', 'code')->toArray();
            }
            return [];
        });
    }

    public static function getRegencies(string $provinceCode): array
    {
        if (empty($provinceCode)) {
            return [];
        }

        return Cache::remember("api_regencies_{$provinceCode}", 3600, function () use ($provinceCode) {
            $data = self::makeRequest("https://wilayah.id/api/regencies/{$provinceCode}.json");
            if ($data) {
                return collect($data)->pluck('name', 'code')->toArray();
            }
            return [];
        });
    }

    public static function getDistricts(string $regencyCode): array
    {
        if (empty($regencyCode)) {
            return [];
        }

        return Cache::remember("api_districts_{$regencyCode}", 3600, function () use ($regencyCode) {
            $data = self::makeRequest("https://wilayah.id/api/districts/{$regencyCode}.json");
            if ($data) {
                return collect($data)->pluck('name', 'code')->toArray();
            }
            return [];
        });
    }

    public static function getVillages(string $districtCode): array
    {
        if (empty($districtCode)) {
            return [];
        }

        return Cache::remember("api_villages_{$districtCode}", 3600, function () use ($districtCode) {
            $data = self::makeRequest("https://wilayah.id/api/villages/{$districtCode}.json");
            if ($data) {
                return collect($data)->pluck('name', 'code')->toArray();
            }
            return [];
        });
    }


    public static function getNameByCode(array $options, string $code): ?string
    {
        return $options[$code] ?? null;
    }

    /**
     * Find province code by name (for edit forms)
     */
    public static function findProvinceCode(string $name): ?string
    {
        $provinces = self::getProvinces();
        $code = array_search($name, $provinces);
        return $code !== false ? $code : null;
    }

    /**
     * Find regency code by name within a province (for edit forms)
     */
    public static function findRegencyCode(string $provinceCode, string $name): ?string
    {
        $regencies = self::getRegencies($provinceCode);
        $code = array_search($name, $regencies);
        return $code !== false ? $code : null;
    }

    /**
     * Find district code by name within a regency (for edit forms)
     */
    public static function findDistrictCode(string $regencyCode, string $name): ?string
    {
        $districts = self::getDistricts($regencyCode);
        $code = array_search($name, $districts);
        return $code !== false ? $code : null;
    }

    /**
     * Find village code by name within a district (for edit forms)
     */
    public static function findVillageCode(string $districtCode, string $name): ?string
    {
        $villages = self::getVillages($districtCode);
        $code = array_search($name, $villages);
        return $code !== false ? $code : null;
    }
}

