<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools;

use ArrayIterator;
use NspTeam\Component\Tools\Interfaces\Arrayable;
use NspTeam\Component\Tools\Interfaces\Jsonable;
use NspTeam\Component\Tools\Utils\ArrayUtil;
use NspTeam\Component\Tools\Utils\StrUtil;
use Traversable;

/**
 * Collection
 *
 * @package NspTeam\Component\Tools
 */
class Collection implements \ArrayAccess, \Countable, \IteratorAggregate, \JsonSerializable, Arrayable, Jsonable
{
    /**
     * @var array
     */
    protected $items = [];

    public function __construct($items = [])
    {
        $this->items = $this->convertToArray($items);
    }

    public static function make($items = []): self
    {
        return new static($items);
    }

    /**
     * 是否为空
     *
     * @access public
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function all(): array
    {
        return $this->items;
    }

    /**
     * 合并数组
     *
     * @param  mixed $items 数据
     * @return static
     */
    public function merge($items): self
    {
        return new static(array_merge($this->items, $this->convertToArray($items)));
    }

    /**
     * 交换数组中的键和值
     *
     * @access public
     * @return static
     */
    public function flip(): self
    {
        return new static(array_flip($this->items));
    }

    /**
     * 返回数组中所有的键名
     *
     * @access public
     * @return static
     */
    public function keys(): self
    {
        return new static(array_keys($this->items));
    }

    /**
     * 返回数组中所有的值组成的新 Collection 实例
     *
     * @access public
     * @return static
     */
    public function values(): self
    {
        return new static(array_values($this->items));
    }

    /**
     * 以相反的顺序返回数组。
     *
     * @access public
     * @return static
     */
    public function reverse(): self
    {
        return new static(array_reverse($this->items));
    }

    /**
     * 删除数组中首个元素，并返回被删除元素的值
     *
     * @access public
     * @return mixed
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * 删除数组的最后一个元素（出栈）,并返回被删除元素的值
     *
     * @access public
     * @return mixed
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * 将数组打乱
     *
     * @access public
     * @return static
     */
    public function shuffle(): self
    {
        $items = $this->items;
        shuffle($items);
        return new static($items);
    }

    /**
     * 对数组排序
     *
     * @access public
     * @param  callable|null $callback 回调
     * @return static
     */
    public function sort(callable $callback = null): self
    {
        $items = $this->items;

        $callback = $callback ?: static function ($a, $b) {
            if ($a === $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        };

        uasort($items, $callback);

        return new static($items);
    }

    /**
     * 指定字段排序
     *
     * @access public
     * @param  string $field 排序字段
     * @param  string $order 排序
     * @return static
     */
    public function order(string $field, string $order = 'asc'): self
    {
        return $this->sort(
            function ($a, $b) use ($field, $order) {
                $fieldA = $a[$field] ?? null;
                $fieldB = $b[$field] ?? null;

                return 'desc' === strtolower($order) ? (int)($fieldB > $fieldA) : (int)($fieldA > $fieldB);
            }
        );
    }

    /**
     * 通过使用用户自定义函数，以字符串返回数组
     *
     * @access public
     * @param  callable $callback 调用方法
     * @param  mixed    $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }

    /**
     * 截取数组
     *
     * @access public
     * @param  int      $offset       起始位置
     * @param  int|null $length       截取长度
     * @param  bool     $preserveKeys preserveKeys
     * @return static
     */
    public function slice(int $offset, int $length = null, bool $preserveKeys = false): self
    {
        return new static(array_slice($this->items, $offset, $length, $preserveKeys));
    }

    /**
     * 返回数据中指定的一列
     *
     * @access public
     * @param  string|null $columnKey 键名
     * @param  string|null $indexKey  作为索引值的列
     * @return array
     */
    public function column(?string $columnKey, string $indexKey = null): array
    {
        return array_column($this->items, $columnKey, $indexKey);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * @inheritDoc
     * @param      mixed $offset
     * @return     mixed
     */
    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    /**
     * @inheritDoc
     * @param      mixed $offset
     * @param      mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    /**
     * @inheritDoc
     * @param      mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    /**
     * @inheritDoc
     * @return     int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @inheritDoc
     * @return     Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     * @return     array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     * @return     array
     */
    public function toArray(): array
    {
        return array_map(
            static function ($value) {
                return $value instanceof Arrayable ? $value->toArray() : $value;
            }, $this->items
        );
    }

    /**
     * @inheritDoc
     * @param      int $options
     * @return     string
     * @throws     \JsonException
     */
    public function toJson(int $options = JSON_UNESCAPED_UNICODE): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR | $options);
    }

    /**
     * @throws \JsonException
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * 转换成数组
     *
     * @param  mixed $items 数据
     * @return array
     */
    protected function convertToArray($items): array
    {
        if ($items instanceof self) {
            return $items->all();
        }

        return (array)$items;
    }
}