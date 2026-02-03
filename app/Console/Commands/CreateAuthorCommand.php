<?php

namespace App\Console\Commands;

use App\Models\Author;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateAuthorCommand extends Command
{
    protected $signature = 'app:create-author';
    protected $description = 'Tworzy nowego autora na podstawie danych z konsoli';

    public function handle(): int
    {
        $name = $this->ask('Podaj imię i nazwisko autora');

        $validator = Validator::make(['name' => $name], [
            'name' => 'required|string|min:3|max:255|unique:authors,name',
        ]);

        if ($validator->fails()) {
            $this->error('Błąd walidacji:');
            foreach ($validator->errors()->all() as $error) {
                $this->line("- $error");
            }
            return self::FAILURE;
        }

        $author = Author::create([
            'name' => $name,
        ]);

        $this->info("Sukces! Autor '{$author->name}' (ID: {$author->id}) został utworzony.");

        return self::SUCCESS;
    }
}
