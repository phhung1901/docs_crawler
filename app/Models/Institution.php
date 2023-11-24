<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Models\Institution
 *
 * @property int $id
 * @property string $name
 * @property string|null $global_name
 * @property string|null $slug
 * @property string|null $code
 * @property string $type university/school
 * @property string $country
 * @property string|null $address
 * @property string|null $website
 * @property string|null $domain
 * @property int|null $crawl_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCrawlStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereGlobalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereWebsite($value)
 * @mixin \Eloquent
 */
class Institution extends Model
{
    protected $table = 'institutions';

    protected $guarded = ['id'];

    public function save(array $options = []) {
        if (!$this->slug) {
            $this->slug = Str::slug($this->name);
        }

        return parent::save($options);
    }
}
