<?php

namespace SleepingOwl\Admin\Display;

use KodiComponents\Support\HtmlAttributes;
use Request;
use SleepingOwl\Admin\Contracts\ColumnInterface;
use SleepingOwl\Admin\Contracts\Display\TableHeaderColumnInterface;
use SleepingOwl\Admin\Display\Column\NamedColumn;
use URL;

class TableHeaderColumn implements TableHeaderColumnInterface
{
    use HtmlAttributes;

    /**
     * Header title.
     * @var string
     */
    protected $title;

    /**
     * Is column orderable?
     * @var bool
     */
    protected $orderable = true;

    /**
     * @var string|\Illuminate\View\View
     */
    protected $view = 'column.header';

    /**
     * @var ColumnInterface|NamedColumn
     */
    protected $column;

    /**
     * TableHeaderColumn constructor.
     *
     * @param ColumnInterface $column
     */
    public function __construct(ColumnInterface $column)
    {
        $this->setHtmlAttribute('class', 'row-header');
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return bool
     */
    public function isOrderable()
    {
        return ($this->column instanceof NamedColumn) && $this->orderable;
    }

    /**
     * @param bool $orderable
     *
     * @return $this
     */
    public function setOrderable($orderable)
    {
        $this->orderable = (bool) $orderable;

        return $this;
    }

    /**
     * @return \Illuminate\View\View|string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param \Illuminate\View\View|string $view
     *
     * @return $this
     */
    public function setView($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'attributes' => $this->htmlAttributesToString(),
            'title' => $this->getTitle(),
            'isOrderable' => $this->isOrderable(),
            'isOrdered' => $this->isOrdered(),
            'orderURL' => $this->getOrderURL(),
            'direction' => $this->getDirection()
        ];
    }

    /**
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        $this->setHtmlAttribute('data-orderable', $this->isOrderable() ? 'true' : 'false');

        return app('sleeping_owl.template')->view($this->getView(), $this->toArray());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
     * @return bool
     */
    public function isOrdered()
    {
        if (! $this->isOrderable()) {
            return false;
        }

        if (Request::input('order') == $this->column->getName()) {
            return true;
        }

        return false;
    }

    /**
     * Returns the default sorting
     * @return string
     */
    public function getDirection()
    {
        if ($this->isOrdered()) {
            // If the column is currently being sorted, grab the direction from the query string
            return Request::input('direction') == 'asc' ? 'asc' : 'desc';
        }

        return 'asc';
    }

    protected function getOrderURL($direction = false)
    {
        if (! $this->isOrderable()) {
            return null;
        }

        if (empty($direction)) {
            $direction = $this->getDirection();
        }

        // Grab the current URL
        $path = URL::getRequest()->path();
        return url($path . '?' . http_build_query(array_filter([
            'order' => $this->column->getName(),
            'direction' => Request::input('direction') == 'asc' ? 'desc' : 'asc',
        ])));
    }
}
