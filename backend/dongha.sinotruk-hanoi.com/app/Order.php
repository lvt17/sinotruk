<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\ProductLog;
use Illuminate\Support\Facades\File;

class Order extends Model
{
    protected $fillable = ['user_id',
        'created_at',
        'money',
        'tenphieu',
        'lock',
        'update_id',
        'nametq', 'invisible', 'completed',
        'vanchuyen', 'tygia', 'loinhuan',
    ];

    public function products() {
        return $this->belongsToMany(Product::class, 'order_product')->withTrashed()
            ->withPivot('id', 'count', 'price', 'note', 'nametq', 'hscode', 'weight', 'image', 'suggest_price', 'hightlight', 'base_number')
            ->orderBy('order_product.id');
    }

    public function user() {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function user_update() {
        return $this->belongsTo(User::class, 'update_id')->withTrashed();
    }

    public function updateCompleted($value) {
        \DB::beginTransaction();
        $this->completed = $value;
        $this->save();
        if ($this->completed == true) {
            $this->removeOrderPendingInProduct("Completed");
        }
        else {
            $this->addOrderPendingInProduct("Completed -> Pending");
        }
        \DB::commit();
    }

    public static function createOrder($request) {
        \DB::beginTransaction();
        $order = Order::create([
            'money' => $request->money,
            'user_id' => auth()->id(),
            'tenphieu' => $request['tenphieu'],
            'vanchuyen' => empty($request['vanchuyen']) ? 0 : $request['vanchuyen'],
            'tygia' => empty($request['tygia']) ? 0 : $request['tygia'],
            'loinhuan' => empty($request['loinhuan']) ? 0 : $request['loinhuan'],
            'invisible' =>true,
        ]);
        if ($request->deletedImages != null) {
            foreach ($request->deletedImages as $deletedImage) {
                File::delete($deletedImage);
            }
        }
        foreach ($request->items as $item) {
            $item['image_pivot'] = Order::moveImage($item, $request['tenphieu']);

            $order->products()->attach($item['id'], [
                'price' => $item['price'],
                'count' => empty($item['count']) ? 0 : $item['count'], // Số lượng thực nhận Received
                'base_number' => empty($item['base_number']) ? 0 : $item['base_number'], // Số lượng Order ban đầu
				'nametq' => empty($item['nametq']) ? 0 : $item['nametq'], //Ratio
                'note' => empty($item['note']) ? '' : $item['note'],
                'hscode' => empty($item['hscode']) ? 0 : $item['hscode'], // Số lượng TQ gửi đi Sent
                'weight' => empty($item['weight']) ? 0 : $item['weight'],
                'image' => empty($item['image_pivot']) ? null : $item['image_pivot'],
                'hightlight' => empty($item['hightlight']) ? 0 : $item['hightlight'],
            ]);

            $product = Product::find($item['id']);
            //$old_order_pending = $product->order_pending;
            $product->order_pending += $item['hscode'];
            $product->save();
        }
        \DB::commit();
        return $order;
    }

    public function updateOrder($request) {
        \DB::beginTransaction();
        $oldTenPhieu = $this->tenphieu;
        $this->tenphieu = $request->tenphieu;
        $this->money = $request->money;
        $this->update_id = auth()->id();
        $this->vanchuyen = empty($request['vanchuyen']) ? $this->vanchuyen : $request['vanchuyen'];
        $this->tygia = empty($request['tygia']) ? $this->tygia : $request['tygia'];
        $this->loinhuan = empty($request['loinhuan']) ? $this->loinhuan : $request['loinhuan'];
        $this->removeOrderPendingInProduct("Xóa");

        $oldFolderName = null;
        $newFolderName = "img/orders/" . $this->tenphieu . "_" . Carbon::now()->format('dmy');
        foreach ($this->products as $product) {
            if ($product->pivot->image != null) {
                $oldFolderName = substr($product->pivot->image, 0, strrpos($product->pivot->image, '/'));
                break;
            }
        }


        $this->products()->detach();
        $this->save();
        if ($request->deletedImages != null) {

            foreach ($request->deletedImages as $deletedImage) {
                File::delete($deletedImage);
            }
        }
        foreach ($request->items as $item) {

            $item['image_pivot'] = Order::moveImage($item, $request['tenphieu']);

            $this->products()->attach($item['id'], [
                'price' => $item['price'],
                'count' => empty($item['count']) ? 0 : $item['count'], // Số lượng thực nhận Received
                'base_number' => empty($item['base_number']) ? 0 : $item['base_number'], // Số lượng Order ban đầu
				'nametq' => empty($item['nametq']) ? 0 : $item['nametq'], //Ratio
                'note' => empty($item['note']) ? '' : $item['note'],
                'hscode' => empty($item['hscode']) ? 0 : $item['hscode'], // Số lượng TQ gửi đi Sent
                'weight' => empty($item['weight']) ? 0 : $item['weight'],
                'image' => empty($item['image_pivot']) ? null : $item['image_pivot'],
                'suggest_price' => empty($item['suggest_price']) ? 0 : $item['suggest_price'],
                'hightlight' => empty($item['hightlight']) ? 0 : $item['hightlight'],
            ]);
            $product = Product::find($item['id']);
            $product->order_pending += $item['hscode'];
            $product->save();
        }

        if ($oldFolderName != $newFolderName)
            File::deleteDirectory($oldFolderName);
        \DB::commit();
    }

    public function deleteOrder() {
        \DB::beginTransaction();
        $folderName = null;
        foreach ($this->products as $product)
        {
            if ($product->pivot->image != null) {
                $folderName = substr($product->pivot->image, 0, strrpos( $product->pivot->image, '/'));
                break;
            }
        }
        if ($folderName != null)
            File::deleteDirectory($folderName);
        if ($this->completed == false) {
            $this->removeOrderPendingInProduct("Xóa");
        }
        $this->products()->detach();
        $this->delete();
        \DB::commit();
    }

    public function removeOrderPendingInProduct($status) {
        foreach ($this->products as $product) {
            $product->order_pending -= $product->pivot->hscode;
            if ($product->order_pending < 0)
                $product->order_pending = 0;
            $product->save();
        }
    }

    public function addOrderPendingInProduct($status) {
        foreach ($this->products as $product) {
            $product->order_pending += $product->pivot->hscode;
            $product->save();
        }
    }

    public static function moveImage($item, $tenphieu) {
        if (!empty($item['image_pivot'])) {
            $file = $item['image_pivot'];
            $folder_path = "img/orders/" . $tenphieu . "_" . Carbon::now()->format('dmy') . "/";
            if(!File::exists($folder_path)) {
                File::makeDirectory($folder_path, 0777, true, true);
            }
            $file_new = $folder_path . $item['code'] . "_" . time() . ".jpg";
            File::move($file,$file_new);
            $item['image_pivot'] = $file_new;
        }
        return $item['image_pivot'];
    }
}
