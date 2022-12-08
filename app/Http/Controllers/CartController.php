<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * @OA\Examples(
     *    summary="addToCart",
     *    example = "addToCart",
     *    value = {
     *        "user_id":"ID of the user(integer)",
     *        "product_id":"ID of the product which needs to be added in the cart(integer)",
     *        "quantity":"Quantity of the product(integer)",
     *        "product_mrp":"MRP of the product(integer)"
     *    }
     *  )
     * 
     * @OA\Post(
     *      path="/addToCart",
     *      operationId="addToCart",
     *      tags={"Cart"},
     *      summary="Add product to cart",
     *      description="Add product to cart",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          examples = {
     *              "addToCart" : @OA\Schema( ref="#/components/examples/addToCart", example="addToCart")
     *          })
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Item added to cart successfully"
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Some error occured"
     *      ),
     *      @OA\Response(
     *          response=303,
     *          description="Item already exists in Cart"
     *      )
     * )
     */
    public function addToCart(Request $request) {
        try {
            $existingItem = Cart::where("product_id", $request["product_id"])
                                ->where("user_id", $request["user_id"])->first();
            
            if($existingItem) {
                return response()->json(["message" => "Item already exists in Cart"], 303);
            } else {
                $cart = Cart::create([
                    "user_id" => $request["user_id"],
                    "product_id" => $request["product_id"],
                    "quantity" => $request["quantity"],
                    "product_mrp" => $request["product_mrp"],
                    "total_mrp" => $request["product_mrp"]*$request["quantity"],
                ]);
                if($cart) {
                    $response = array(
                        "message" => "Item added to cart successfully",
                        "status" => 200,
                    );
                    return response()->json($response);
                } else {
                    return response()->json(["message" => "Some error occured"], 500);
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @OA\Examples(
     *    summary="updateProductQuantityInCart",
     *    example = "updateProductQuantityInCart",
     *    value = {
     *        "user_id":"ID of the user(integer)",
     *        "product_id":"ID of the product which needs to be added in the cart(integer)",
     *        "quantity":"Quantity of the product(integer)",
     *    }
     *  )
     * 
     * @OA\Post(
     *      path="/updateProductQuantityInCart",
     *      operationId="updateProductQuantityInCart",
     *      tags={"Cart"},
     *      summary="Update Product Quantity in Cart",
     *      description="Update Product Quantity in Cart",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *          examples = {
     *              "updateProductQuantityInCart" : @OA\Schema( ref="#/components/examples/updateProductQuantityInCart", example="updateProductQuantityInCart")
     *          })
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Item updated/removed in/from cart successfully"
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Some error occured"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product not found in cart"
     *      )
     * )
     */
    public function updateProductQuantityInCart(Request $request) {
        try {
            $cart = Cart::where("product_id", $request["product_id"])
                        ->where("user_id", $request["user_id"])->first();
            if($request["quantity"] == 0) {
                $cart->delete();
                return response()->json(["message" => "Item removed from cart"], 200);
            }
            if($cart) {
                $updatedCart = $cart->update([
                    "quantity" => $request["quantity"],
                    "total_mrp" => $request["quantity"]*$cart->product_mrp
                ]);
            } else {
                return response()->json(["message" => "Product not found in cart"], 404);
            }
            
            if($updatedCart) {
                $response = array(
                    "message" => "Item updated in cart successfully",
                    "status" => 200,
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "Some error occured"], 500);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * @OA\Delete(
     *      path="/emptyCart/{userId}",
     *      operationId="emptyCart",
     *      tags={"Cart"},
     *      summary="Empty Cart",
     *      description="Remove every product from Cart",
     *      @OA\Parameter(
     *          name="userId",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Cart emptied successfully"
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="No cart found for the user"
     *      )
     * )
     */
    public function emptyCart($userId) {
        try {
            $cart = Cart::where("user_id", $userId)->get();
            if($cart) {
                $cart->each->delete();
                $response = array(
                    "message" => "Cart emptied successfully",
                    "status" => 200,
                );
                return response()->json($response);
            } else {
                return response()->json(["message" => "No cart found for the user"], 404);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
