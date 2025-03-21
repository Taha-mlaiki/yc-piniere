<?php

namespace App\Http\Controllers;

use App\DAO\PlantDAO;
use App\DAO\PlantImageDAO;
use App\DTO\PlantDTO;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlantController extends Controller
{
    protected $plantDAO;
    protected $plantImageDAO;

    public function __construct(PlantDAO $plantDAO, PlantImageDAO $plantImageDAO)
    {
        $this->plantDAO = $plantDAO;
        $this->plantImageDAO = $plantImageDAO;
    }

    public function index()
    {
        try {
            $plants = $this->plantDAO->findAllWithCategory();
            
            return response()->json([
                'plants' => $plants,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve plants',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $plantDTO = new PlantDTO($request->all());
            
            $plant = $this->plantDAO->create([
                'name' => $plantDTO->name,
                'description' => $plantDTO->description,
                'price' => $plantDTO->price,
                'stock' => $plantDTO->stock,
                'category_id' => $plantDTO->category_id,
            ]);

            return response()->json([
                'message' => 'Plant created successfully',
                'plant' => $plant,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create plant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            $plant = $this->plantDAO->findBySlug($slug);
            
            if (!$plant) {
                return response()->json([
                    'message' => 'Plant not found',
                ], 404);
            }

            return response()->json([
                'plant' => $plant,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve plant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $plant = $this->plantDAO->find($id);
            
            if (!$plant) {
                return response()->json([
                    'message' => 'Plant not found',
                ], 404);
            }

            $plantDTO = new PlantDTO($request->all());
            
            $this->plantDAO->update($plant, [
                'name' => $plantDTO->name,
                'description' => $plantDTO->description,
                'price' => $plantDTO->price,
                'stock' => $plantDTO->stock,
                'category_id' => $plantDTO->category_id,
            ]);

            return response()->json([
                'message' => 'Plant updated successfully',
                'plant' => $plant->fresh(),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update plant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $plant = $this->plantDAO->find($id);
            
            if (!$plant) {
                return response()->json([
                    'message' => 'Plant not found',
                ], 404);
            }

            $this->plantDAO->delete($plant);

            return response()->json([
                'message' => 'Plant deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete plant',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadImage(Request $request, $id)
    {
        try {
            $plant = $this->plantDAO->find($id);
            
            if (!$plant) {
                return response()->json([
                    'message' => 'Plant not found',
                ], 404);
            }

            $request->validate([
                'image' => 'required|image|max:2048',
                'is_main' => 'sometimes|boolean',
            ]);

            // Check if the plant already has 4 images
            $imageCount = $this->plantImageDAO->countImagesForPlant($plant->id);
            if ($imageCount >= 4) {
                return response()->json([
                    'message' => 'Limite de 4 images par plante dépassée',
                ], 422);
            }

            $path = $request->file('image')->store('plants', 'public');
            
            $plantImage = $this->plantImageDAO->addImage(
                $plant->id,
                $path,
                $request->is_main ?? false
            );

            return response()->json([
                'message' => 'Image uploaded successfully',
                'image' => $plantImage,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to upload image',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}




