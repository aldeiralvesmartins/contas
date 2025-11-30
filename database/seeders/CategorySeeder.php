<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Categorias de Entrada (Income)
            [
                'name' => 'Salário',
                'type' => 'income',
                'color' => '#10b981',
                'icon' => '💰',
                'description' => 'Renda mensal fixa'
            ],
            [
                'name' => 'Freelance',
                'type' => 'income',
                'color' => '#3b82f6',
                'icon' => '💼',
                'description' => 'Trabalhos freelancer'
            ],
            [
                'name' => 'Investimentos',
                'type' => 'income',
                'color' => '#8b5cf6',
                'icon' => '📈',
                'description' => 'Rendimentos de investimentos'
            ],
            [
                'name' => 'Bônus',
                'type' => 'income',
                'color' => '#f59e0b',
                'icon' => '🎁',
                'description' => 'Bônus e gratificações'
            ],
            [
                'name' => 'Outras Receitas',
                'type' => 'income',
                'color' => '#6b7280',
                'icon' => '📥',
                'description' => 'Outras fontes de renda'
            ],

            // Categorias de Saída (Expense)
            [
                'name' => 'Alimentação',
                'type' => 'expense',
                'color' => '#ef4444',
                'icon' => '🍔',
                'description' => 'Supermercado e alimentação'
            ],
            [
                'name' => 'Transporte',
                'type' => 'expense',
                'color' => '#f59e0b',
                'icon' => '🚗',
                'description' => 'Combustível, transporte público'
            ],
            [
                'name' => 'Moradia',
                'type' => 'expense',
                'color' => '#8b5cf6',
                'icon' => '🏠',
                'description' => 'Aluguel, condomínio, IPTU'
            ],
            [
                'name' => 'Educação',
                'type' => 'expense',
                'color' => '#3b82f6',
                'icon' => '📚',
                'description' => 'Cursos, livros, material escolar'
            ],
            [
                'name' => 'Saúde',
                'type' => 'expense',
                'color' => '#ec4899',
                'icon' => '🏥',
                'description' => 'Consultas, medicamentos, plano de saúde'
            ],
            [
                'name' => 'Lazer',
                'type' => 'expense',
                'color' => '#14b8a6',
                'icon' => '🎮',
                'description' => 'Entretenimento, hobbies'
            ],
            [
                'name' => 'Vestuário',
                'type' => 'expense',
                'color' => '#6366f1',
                'icon' => '👕',
                'description' => 'Roupas, calçados, acessórios'
            ],
            [
                'name' => 'Contas',
                'type' => 'expense',
                'color' => '#ef4444',
                'icon' => '💡',
                'description' => 'Luz, água, internet, telefone'
            ],
            [
                'name' => 'Outras Despesas',
                'type' => 'expense',
                'color' => '#6b7280',
                'icon' => '📤',
                'description' => 'Outras despesas diversas'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
