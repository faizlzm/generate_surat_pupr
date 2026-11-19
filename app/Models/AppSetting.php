<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AppSetting extends Model
{
    /** @use HasFactory<\Database\Factories\AppSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value_json',
    ];

    protected function casts(): array
    {
        return [
            'value_json' => 'array',
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();
        if (! $setting) {
            return $default;
        }
        $value = $setting->value_json;

        return $value ?? $default;
    }

    public static function setValue(string $key, mixed $value): self
    {
        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value_json' => $value],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $stored = static::getValue('general', []);
        $stored = is_array($stored) ? $stored : [];

        return array_replace_recursive(static::defaultGeneral(), $stored);
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function saveDefaults(array $values): void
    {
        $merged = array_replace_recursive(static::defaultGeneral(), $values);
        static::setValue('general', $merged);
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultGeneral(): array
    {
        return [
            'ministry' => 'KEMENTERIAN PEKERJAAN UMUM',
            'directorate' => 'DIREKTORAT JENDERAL PRASARANA STRATEGIS',
            'address' => 'Jalan Pattimura No. 20, Selong, Kebayoran Baru, Jakarta Selatan, DKI Jakarta 12110',
            'sender_title' => 'Plt. Direktur Jenderal Prasarana Strategis',
            'subject' => 'Teguran Tertulis',
            'signatory_title' => 'Plt. Direktur Jenderal Prasarana Strategis',
            'signatory_name' => 'Kuswara',
            'cc_list' => [
                'Plt. Sekretaris Direktorat Jenderal Prasarana Strategis',
                'Direktur Kepatuhan Intern, Direktorat Jenderal Prasarana Strategis',
            ],
            'instruction_template' => 'Untuk selanjutnya, kami instruksikan {{sapaan}} agar lebih cermat dalam melakukan pengawasan dan pengendalian pada perencanaan dan pelaksanaan pekerjaan.',
            'logo_path' => null,
        ];
    }
}
