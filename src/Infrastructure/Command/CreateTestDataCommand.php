<?php

declare(strict_types=1);

namespace Command;

use App\Domain\Entity\Category;
use App\Domain\Entity\Product;
use Ramsey\Uuid\Uuid;
use Repository\CategoryRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-test-data',
    description: 'Create test data for the application'
)]
class CreateTestDataCommand extends Command
{
    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly ProductRepositoryInterface  $productRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Creating test data');

        // Create categories
        $categories = [];

        $categoryData = [
            ['code' => 'ELEC'],
            ['code' => 'BOOK'],
            ['code' => 'CLOTH'],
            ['code' => 'FOOD'],
        ];

        $io->section('Creating categories');
        foreach ($categoryData as $data) {
            $code = new CategoryCode($data['code']);

            if (!$this->categoryRepository->isCodeUnique($code)) {
                $io->text('Category with code ' . $data['code'] . ' already exists, skipping...');
                continue;
            }

            $category = new Category(Uuid::uuid4(), $code);
            $this->categoryRepository->save($category);
            $categories[] = $category;

            $io->text('Created category: ' . $data['code']);
        }

        $productData = [
            ['name' => 'Laptop', 'price' => 1999.99, 'categories' => ['ELEC']],
            ['name' => 'Smartphone', 'price' => 999.99, 'categories' => ['ELEC']],
            ['name' => 'T-shirt', 'price' => 29.99, 'categories' => ['CLOTH']],
            ['name' => 'Jeans', 'price' => 59.99, 'categories' => ['CLOTH']],
            ['name' => 'PHP Book', 'price' => 49.99, 'categories' => ['BOOK']],
            ['name' => 'Chocolate', 'price' => 5.99, 'categories' => ['FOOD']],
            ['name' => 'Keyboard', 'price' => 89.99, 'categories' => ['ELEC']],
            ['name' => 'Mouse', 'price' => 49.99, 'categories' => ['ELEC']],
            ['name' => 'Headphones', 'price' => 149.99, 'categories' => ['ELEC']],
            ['name' => 'Monitor', 'price' => 299.99, 'categories' => ['ELEC']],
        ];

        $io->section('Creating products');
        foreach ($productData as $data) {
            $product = new Product(Uuid::uuid4(), $data['name'], $data['price']);

            foreach ($data['categories'] as $categoryCode) {
                $category = $this->categoryRepository->findByCode(new CategoryCode($categoryCode));
                if ($category) {
                    $product->addCategory($category);
                }
            }

            $this->productRepository->save($product);

            $io->text('Created product: ' . $data['name'] . ' with price ' . $data['price'] . ' in categories: ' . implode(', ', $data['categories']));
        }

        $io->success('Test data created successfully');

        return Command::SUCCESS;
    }
}