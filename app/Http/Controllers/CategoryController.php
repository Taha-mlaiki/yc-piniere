<?php

namespace App\Http\Controllers;

use App\DAO\CategoryDAO;
use App\DTO\CategoryDTO;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    protected $categoryDAO;

    public function __construct(CategoryDAO $categoryDAO)
    {
        $this->categoryDAO = $categoryDAO;
    }

    public function index()
    {
        try {
            $categories = $this->categoryDAO->all();

            return response()->json([
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve categories',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $categoryDTO = new CategoryDTO($request->all());

            $category = $this->categoryDAO->create([
                'name' => $categoryDTO->name,
                'description' => $categoryDTO->description,
            ]);

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $category = $this->categoryDAO->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found',
                ], 404);
            }

            return response()->json([
                'category' => $category,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = $this->categoryDAO->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found',
                ], 404);
            }

            $categoryDTO = new CategoryDTO($request->all());

            $this->categoryDAO->update($category, [
                'name' => $categoryDTO->name,
                'description' => $categoryDTO->description,
            ]);

            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category->fresh(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = $this->categoryDAO->find($id);

            if (!$category) {
                return response()->json([
                    'message' => 'Category not found',
                ], 404);
            }

            $this->categoryDAO->delete($category);

            return response()->json([
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
