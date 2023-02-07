<?php

declare(strict_types=1);
/**
 * @Auth       Ucdo
 * @framework  Hyperf
 */
namespace App\Model;

use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Model\Model as BaseModel;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Str;

abstract class Model extends BaseModel
{
    public function createOne(array $data, bool $getId = false): int|bool
    {
        foreach ($data as &$v) {
            is_array($v) && Json::encode($v);
        }
        unset($v);
        if ($getId) {
            return static::insertGetId($data);
        }

        return static::insert($data);
    }

    public function createAll(array $data): bool
    {
        foreach ($data as &$v) {
            foreach ($v as $vv) {
                is_array($vv) && Json::encode($vv);
            }
            unset($vv);
        }
        unset($v);
        return static::insert($data);
    }

    public function deleteByWhere(array $where): int
    {
        return (int) $this->optionWhere([], $where)->delete();
    }

    public function updateByWhere(array $where, array $data): int
    {
        $newData = $this->columnsFormat($data, true, true);
        foreach ($newData as $item => &$value) {
            if (is_array($value)) {
                $value = Json::encode($value);
            }
        }
        unset($value);
        $this->reSetAttribute($newData);
        return (int) $this->optionWhere([], $where)->update($newData);
    }

    public function getOneByWhere(array $where, array $columns, array $options): array
    {
        $data = $this->optionWhere($options, $where)->first($columns);
        $data || $data = collect();
        return $data->toArray();
    }

    public function getManyByWhere(array $where, array $columns, array $options): array
    {
        $data = $this->optionWhere($where, $options)->get($columns);
        $data || $data = collect();
        return $data->toArray();
    }

    public function paginateByWhere(array $where, array $columns, array $options): array
    {
        $model = $this->optionWhere($where, $options);

        # # 分页参数
        $perPage = isset($options['perPage']) ? (int) $options['perPage'] : 15;
        $pageName = $options['pageName'] ?? 'page';
        $page = isset($options['page']) ? (int) $options['page'] : null;
        # # 分页
        $data = $model->paginate($perPage, $columns, $pageName, $page);
        $data || $data = collect([]);
        return $data->toArray();
    }

    public function optionWhere(array $option, array $where): mixed
    {
        $model = new static();

        foreach ($where as $v) {
            $v[1] = mb_strtoupper($v[1]);
            if (in_array($v[1], ['<', '=', '<=', '>=', '>', '!=', 'LIKE', 'NOT LIKE'])) {
                $model = $model->where($v[0], $v[1], $v[2]);
            } elseif ($v[1] == 'NULL') {
                $model = $model->whereNull($v[0]);
            } elseif ($v[1] == 'NOT NULL') {
                $model = $model->whereNotNull($v[0]);
            } elseif ($v[1] == 'IN') {
                $model = $model->whereIn($v[0], $v[2]);
            } elseif ($v[1] == 'NOT IN') {
                $model = $model->whereNotNull($v[0], $v[2]);
            } elseif ($v[1] == 'BETWEEN') {
                $model = $model->whereBetween($v[0], $v[2]);
            }
        }

        ! empty($option['orderByRaw']) && $model = $model->orderByRaw($option['orderBy']);
        ! empty($options['groupByRaw']) && $model = $model->groupBy((array) $options['groupByRaw']);
        ! empty($option['with']) && $model = $model->with($option['with']);

        return $model;
    }

    /**
     * 格式化表字段.
     * @param array $value ...
     * @param bool $isTransSnake 是否转snake
     * @param bool $isColumnFilter 是否过滤表不存在的字段
     * @return array ...
     */
    public function columnsFormat(array $value, bool $isTransSnake = false, bool $isColumnFilter = false): array
    {
        $formatValue = [];
        $isColumnFilter && $tableColumns = array_flip(Schema::getColumnListing($this->getTable()));
        foreach ($value as $field => $fieldValue) {
            # # 转snake
            $isTransSnake && $field = Str::snake($field);
            # # 过滤
            if ($isColumnFilter && ! isset($tableColumns[$field])) {
                continue;
            }
            $formatValue[$field] = $fieldValue;
        }
        return $formatValue;
    }
}
