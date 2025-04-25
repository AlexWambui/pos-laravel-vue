<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class RestaurantController extends Controller
{
    /**
     * Display a listing of restaurants.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $restaurants = Restaurant::query()
            ->select([
                'id',
                'name',
                'slug',
                'location',
                'phone',
                'email',
                'is_active',
            ])
            ->get();

        return response()->json($restaurants);
    }

    /**
     * Store a newly created restaurant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $validated_data = $request->validate([
            'name' => 'required|string|max:100|unique:restaurants,name',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:restaurants,email',
        ]);

        $restaurant = DB::transaction(function () use ($validated_data) {
            return Restaurant::create($validated_data);
        });

        return response()->json($restaurant, Response::HTTP_CREATED);
    }

    /**
     * Display the specified restaurant.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Restaurant $restaurant): JsonResponse
    {
        return response()->json($restaurant);
    }

    /**
     * Update the specified restaurant.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, Restaurant $restaurant): JsonResponse
    {
        $validated_data = $request->validate([
            'name' => 'sometimes|string|max:100|unique:restaurants,name,' . $restaurant->id,
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:restaurants,email,' . $restaurant->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $restaurant = DB::transaction(function () use ($validated_data, $restaurant) {
            $restaurant->update($validated_data);
            return $restaurant->fresh();
        });

        return response()->json($restaurant);
    }

    /**
     * Remove the specified restaurant.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Restaurant $restaurant): JsonResponse
    {
        DB::transaction(function () use ($restaurant) {
            $restaurant->delete();
        });

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
