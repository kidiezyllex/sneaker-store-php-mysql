<?php

class Controller
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Render a view file with provided data.
     */
    protected function render(string $viewPath, array $data = []): void
    {
        extract($data);
        include $viewPath;
    }
}

