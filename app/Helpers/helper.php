<?php

use App\Enums\AdsStatus;
use App\Enums\AuctionStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\PromotionStatus;
use App\Enums\UserType;
use App\Events\InteractionToggled;
use App\Models\Card;
use App\Models\Category;
use App\Models\Interest;
use App\Models\RechargeCard;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet\Payment;
use App\Models\Wallet\Transaction;
use App\Models\Wallet\Wallet;
use App\Models\Wallet\WalletLog;
use App\Services\Payments\CardPayment;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
function removeSession($session)
{
    if (\Session::has($session)) {
        \Session::forget($session);
    }
    return true;
}

function randomString($length, $type = 'token')
{
    if ($type == 'password')
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    elseif ($type == 'username')
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    else
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $token = substr(str_shuffle($chars), 0, $length);
    return $token;
}

function activeRoute($route, $isClass = false): string
{
    $requestUrl = request()->fullUrl() === $route ? true : false;

    if ($isClass) {
        return $requestUrl ? $isClass : '';
    } else {
        return $requestUrl ? 'active' : '';
    }
}

function checkRecordExist($table_list, $column_name, $id)
{
    if (count($table_list) > 0) {
        foreach ($table_list as $table) {
            $check_data = \DB::table($table)->where($column_name, $id)->count();
            if ($check_data > 0)
                return false;
        }
        return true;
    }
    return true;
}

// Model file save to storage by spatie media library
function storeMediaFile($model, $file, $name)
{
    if ($file) {
        $model->clearMediaCollection($name);
        if (is_array($file)) {
            foreach ($file as $key => $value) {
                $model->addMedia($value)->toMediaCollection($name);
            }
        } else {
            $model->addMedia($file)->toMediaCollection($name);
        }
    }
    return true;
}

// Model file get by storage by spatie media library
function getSingleMedia($model, $collection = 'image_icon', $skip = true)
{
    if (!\Auth::check() && $skip) {
        return asset('images/avatars/01.png');
    }
    if ($model !== null) {
        $media = $model->getFirstMedia($collection);
    }
    $imgurl = isset($media) ? $media->getPath() : '';
    if (file_exists($imgurl)) {
        return $media->getFullUrl();
    } else {
        switch ($collection) {
            case 'image_icon':
                $media = asset('images/avatars/01.png');
                break;
            case 'profile_image':
                $media = asset('images/avatars/01.png');
                break;
            default:
                $media = asset('images/common/add.png');
                break;
        }
        return $media;
    }
}

// File exist check
function getFileExistsCheck($media)
{
    $mediaCondition = false;
    if ($media) {
        if ($media->disk == 'public') {
            $mediaCondition = file_exists($media->getPath());
        } else {
            $mediaCondition = \Storage::disk($media->disk)->exists($media->getPath());
        }
    }
    return $mediaCondition;
}




if (!function_exists('getLocaleFromRequest')) {
    function getLocaleFromRequest(): string
    {
        $locale = request()->header('Locale', 'ar');
        return in_array($locale, ['en', 'ar']) ? $locale : 'ar';
    }
}
if (!function_exists('getPerPage')) {
    function getPerPage(Request $request, int $defaultPerPage = 25, int $maxPerPage = 200): int
    {
        $perPage = $request->get('per_page', $defaultPerPage);

        if (!is_numeric($perPage)) {
            $perPage = $defaultPerPage;
        }
        return (int) min($perPage, $maxPerPage);
    }
}

if (!function_exists('backWithSuccess')) {
    function backWithSuccess($msg = null, $data = null)
    {
        $locale = getLocaleFromRequest();

        $defaultMessages = [
            'en' => 'Operation completed successfully',
            'ar' => 'تم الأمر بنجاح',
        ];

        $msg = $msg ?? $defaultMessages[$locale];

        $response = ['message' => $msg];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response);
    }
}

if (!function_exists('backWithWarning')) {
    function backWithWarning($ar_message = "", $en_message = "", $code = 422)
    {
        $locale = getLocaleFromRequest();
        $msg = $locale == 'en' ? $en_message : $ar_message;
        return response()->json(['message' => $msg], $code);
    }
}

if (!function_exists('backWithError')) {
    function backWithError($e = null)
    {
        $locale = getLocaleFromRequest();

        $defaultErrorMessages = [
            'en' => 'An error occurred: :msg',
            'ar' => 'حدث خطأ: :msg',
        ];

        if ($e instanceof ValidationException) {
            return response()->json(['message' => $e->errors()], 422);
        }

        $template = $defaultErrorMessages[$locale];
        $message = str_replace(':msg', $e?->getMessage() ?? '', $template);

        return response()->json(['message' => $message], 500);
    }
}

if (!function_exists('getCustomValidationMessages')) {
    function getCustomValidationMessages(): array
    {
        $locale = getLocaleFromRequest();

        $messages = [
            'en' => [
                'required' => 'The :attribute field is required.',
                'starts_with' => 'The :attribute must start with one of the allowed values.',
                'string' => 'The :attribute must be a string.',
                'max' => 'The :attribute may not be greater than :max.',
                'min' => 'The :attribute must be at least :min.',
                'unique' => 'The :attribute has already been taken.',
                'email' => 'The :attribute must be a valid email address.',
                'integer' => 'The :attribute must be an integer.',
                'array' => 'The :attribute must be an array.',
                'exists' => 'The selected :attribute is invalid.',
                'date' => 'The :attribute is not a valid date.',
                'boolean' => 'The :attribute field must be true or false.',
                'confirmed' => 'The :attribute confirmation does not match.',
                'url' => 'The :attribute must be a valid URL.',
                'uuid' => 'The :attribute must be a valid UUID.',
                'regex' => 'The :attribute format is invalid.',
                'mimes' => 'The :attribute must be a file of type: :values.',
                'image' => 'The :attribute must be an image.',
                'size' => 'The :attribute must be :size.',
                'in' => 'The :attribute must be one of the following: :values.',
                'gt' => 'The :attribute must be greater than :value.',
                'lt' => 'The :attribute must be less than :value.',
                'between' => 'The :attribute must be between :min and :max.',
                'nullable' => 'The :attribute field may be null.',
                'same' => 'The :attribute and :other must match.',
                'distinct' => 'The :attribute field has a duplicate value.',
            ],

            'ar' => [
                'required' => 'حقل :attribute مطلوب.',
                'starts_with' => 'حقل :attribute يجب أن يبدأ بأحد القيم المسموح بها.',
                'string' => 'حقل :attribute يجب أن يكون نصًا.',
                'max' => 'حقل :attribute يجب ألا يتجاوز :max.',
                'min' => 'حقل :attribute يجب أن يكون على الأقل :min.',
                'unique' => 'حقل :attribute مسجل بالفعل.',
                'email' => 'حقل :attribute يجب أن يكون بريدًا إلكترونيًا صالحًا.',
                'integer' => 'حقل :attribute يجب أن يكون عددًا صحيحًا.',
                'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
                'exists' => 'حقل :attribute لا يوجد في قاعدة البيانات.',
                'date' => 'حقل :attribute يجب أن يكون تاريخًا صالحًا.',
                'boolean' => 'حقل :attribute يجب أن يكون صحيحًا أو خطأ.',
                'confirmed' => 'حقل :attribute لا يتطابق مع تأكيد :attribute.',
                'url' => 'حقل :attribute يجب أن يكون رابطًا صالحًا.',
                'uuid' => 'حقل :attribute يجب أن يكون UUID صالحًا.',
                'regex' => 'حقل :attribute لا يتطابق مع النمط المحدد.',
                'mimes' => 'حقل :attribute يجب أن يكون من نوع ملف :values.',
                'image' => 'حقل :attribute يجب أن يكون صورة.',
                'size' => 'حقل :attribute يجب أن يكون بحجم :size.',
                'in' => 'حقل :attribute يجب أن يكون أحد القيم التالية: :values.',
                'gt' => 'حقل :attribute يجب أن يكون أكبر من :value.',
                'lt' => 'حقل :attribute يجب أن يكون أصغر من :value.',
                'between' => 'حقل :attribute يجب أن يكون بين :min و :max.',
                'nullable' => 'حقل :attribute يمكن أن يكون فارغًا.',
                'same' => 'حقل :attribute يجب أن يتطابق مع :other.',
                'distinct' => 'حقل :attribute يحتوي على قيم مكررة.',
            ],
        ];

        return $messages[$locale];
    }
}

if (!function_exists('t')) {
    function t($data, $lang = 'en', $fallback = 'en')
    {
        return $data[$lang] ?? $data[$fallback] ?? '';
    }
}
if (!function_exists('setting')) {
    function setting($key, $sub = null)
    {
        static $cache = [];

        if (!isset($cache[$key])) {
            $cache[$key] = Setting::where('key', $key)->value('value');
        }

        $data = $cache[$key];

        if ($sub) {
            return $data[$sub] ?? null;
        }

        return $data;
    }
}
if (!function_exists('successResponse')) {
    function successResponse($message, $data = [], $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }
}
if (!function_exists('errorResponse')) {

    function errorResponse($message, $data = [], $code = 500)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], $code);
    }

}

if (!function_exists('imageDefault')) {
    function imageDefault()
    {
        return env('APP_ENV') == 'production' ? asset('storage/default/default-avatar.png') : asset('storage/default/default-avatar.png');
    }
}

// if (!function_exists('addMediaIfExists')) {
//     function addMediaIfExists($model, $data, $key)
//     {

//         if (!empty($data[$key])) {
//             $model->clearMediaCollection($key); // ? Clear existing media in the collection before adding new one
//             $model->addMedia($data[$key])
//                 ->toMediaCollection($key);
//         }
//     }
// }

use Illuminate\Support\Arr;

if (!function_exists('addMediaIfExists')) {
    function addMediaIfExists($model, $data, $key)
    {
        $files = Arr::wrap($data[$key] ?? []);

        if (empty($files)) {
            return;
        }

        $model->clearMediaCollection($key); // ? Clear existing media in the collection before adding new one

        foreach ($files as $file) {
            $model->addMedia($file)
                ->toMediaCollection($key);
        }
    }
}


function toggleInteraction(string $model, array $where, string $interaction, array $extra = [])
{
    $query = $model::query()->where('user_id', auth()->id());

    foreach ($where as $key => $value) {
        $query->where($key, $value);
    }

    $existing = $query->first();

    // ? resolve parent model using morphMap
    $parent = null;

    foreach ($where as $key => $value) {
        if (str_contains($key, '_type')) {
            $modelClass = Relation::getMorphedModel($value);
            $idKey = str_replace('_type', '_id', $key);
            $parent = $modelClass::find($where[$idKey]);
            break;
        }
    }

    if ($existing) {
        $existing->delete();
        event(new InteractionToggled($parent, 'decrement', $interaction));
        return false;
    }
    $model::create(array_merge($where, $extra, [
        'user_id' => auth()->id(),
    ]));
    event(new InteractionToggled($parent, 'increment', $interaction));
    return true;
}

if (!function_exists('isAdmin')) {
    function isAdmin(User $user): bool
    {
        return $user->user_type === UserType::ADMIN;
    }
}

if (!function_exists('isOwner')) {
    function isOwner(User $user, $model): bool
    {
        return $user->id === $model->user_id;
    }
}

if (!function_exists('isUser')) {
    function isUser(User $user): bool
    {
        return $user->user_type === UserType::USER;
    }
}


if (!function_exists('activityLog')) {
    function activityLog($model, string $description, $properties = [])
    {
        activity()
            ->causedBy(auth()->user())
            ->performedOn($model)
            ->withProperties($properties)
            ->log($description);
    }
}

if (!function_exists('syncReelInterests')) {

    function syncReelInterests($reel): void
    {
        $interestIds = detectInterests($reel->title, $reel->description);
        if (!empty($interestIds)) {
            $reel->interests()->sync($interestIds);
        }
    }
}

if (!function_exists('detectInterests')) {

    function detectInterests(string $title, ?string $description): array
    {

        $text = strtolower(
            trim($title . ' ' . $description)
        );
        $interests = Interest::all();
        $matchedIds = [];

        foreach ($interests as $interest) {

            $keywords = explode(',', strtolower($interest->keywords ?? $interest->name));

            foreach ($keywords as $keyword) {

                if (
                    str_contains(
                        $text,
                        trim($keyword)
                    )
                ) {
                    $matchedIds[] = $interest->id;
                    break;
                }
            }
        }

        return array_unique($matchedIds);
    }
}


if (!function_exists('extractKeywords')) {

    function extractKeywords($text)
    {

        // ? 1. concat title and description
        $text = trim($text);
        // ? 2. word segmentation  
        $words = explode(' ', str_replace('-', ' ', $text));
        $stopWords = ['the', 'and', 'is', 'a', 'to', 'of', 'in'];

        // ? 3. clear short words 
        $keywords = collect($words)
            ->map(fn($word) => strtolower(trim($word)))
            ->filter(fn($word) => $word !== '') // ? remove empty words
            ->reject(fn($word) => in_array($word, $stopWords))
            ->reject(fn($word) => strlen($word) < 3)
            ->unique()
            ->values()
            ->toArray();

        return implode(', ', $keywords);
    }
}

if (!function_exists('payment')) {
    function payment(
        $user_id,
        $merchant_ref,
        $amount,
        $status,
        $payment_gateway = 'moamalat',
        $type,
        $payable_type,
        $payable_id,
        $details = null,
    ) {
        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            |    (Idempotency Check)
            |--------------------------------------------------------------------------
            |  lockForUpdate()  
            */

            $existingPayment = Payment::where('merchant_ref', $merchant_ref)
                ->lockForUpdate()
                ->first();

            if ($existingPayment) {
                DB::rollBack();
                return $existingPayment;
            }

            $payment = Payment::create([
                'user_id' => $user_id,
                'merchant_ref' => $merchant_ref,
                'amount' => $amount,
                'status' => $status,
                'payment_gateway' => $payment_gateway,
                'type' => $type,
                'payable_type' => $payable_type,
                'payable_id' => $payable_id,
                'details' => $details,
            ]);

            DB::commit();
            return $payment->load('payable');

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

if (!function_exists('transaction')) {
    function transaction(
        $user_id,
        $amount,
        $type,
        $status,
        $source_type,
        $source_id,
        $description
    ) {
        $lockKey = "transaction_lock_{$user_id}_{$source_type}_{$source_id}";

        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            throw new Exception(__("messages.transaction_in_progress"));
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'user_id' => $user_id,
                'amount' => $amount,
                'type' => $type,
                'status' => $status,
                'source_type' => $source_type,
                'source_id' => $source_id,
                'description' => $description,
            ]);

            DB::commit();
            return $transaction->load('source');

        } catch (Exception $e) {
            DB::rollBack();
            $lock->release(); // ? release the lock and allow other requests
            throw $e;
        }
    }
}

if (!function_exists('process_payment_callback')) {
    function process_payment_callback(
        $merchant_ref,
        $user_id,
        $amount_paid,
        $payable_id,
        $type,
        $gateway_details = null,
        $description = null
    ) {
        return DB::transaction(function () use ($merchant_ref, $user_id, $amount_paid, $payable_id, $gateway_details, $type, $description) {

            $payment = Payment::where('merchant_ref', $merchant_ref)
                ->where('status', PaymentStatus::PENDING)
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                throw new Exception(__("messages.payment_not_found"));
            }

            if ((int) $payment->user_id !== (int) $user_id) {
                throw new Exception(__("messages.payment_not_found"));
            }

            if ((int) $payment->payable_id !== (int) $payable_id) {
                throw new Exception(__("messages.payment_not_found"));
            }

            if ((float) $payment->amount !== (float) $amount_paid) {
                throw new Exception(__("messages.payment_not_found"));
            }

            //-------------------------------------------------------------
            // ? Process the payment
            //-------------------------------------------------------------
            $payment->update([
                'status' => PaymentStatus::SUCCESS,
                'amount' => $gateway_details['amount'] ?? $payment->amount,
                'details' => $gateway_details ? json_encode($gateway_details) : null
            ]);

            if ($type === PaymentType::WALLET_DEPOSIT->value) {
                $amount = $gateway_details['amount'] ?? $payment->amount;
                app(CardPayment::class)->pay($payment->user, $payment->payable, $amount, $type);
                return $payment->refresh();
            }
            $object = $payment->payable;
            if ($object) {
                $status = ($type === 'moamalat') ? AdsStatus::PENDING->value : (auth()->user()->ads_enabled ? AdsStatus::ACTIVE->value : AdsStatus::REVIEW->value);
                $payment->type === PaymentType::AD_FEE->value ? $object->update(['status' => $status]) : $object->update(['status' => PromotionStatus::ACTIVE]);
            }
            // ? Create a transaction
            transaction(
                user_id: $payment->user_id,
                amount: $payment->amount,
                type: $type,
                status: PaymentStatus::COMPLETED,
                source_type: get_class($payment),
                source_id: $payment->id,
                description: $description
            );

            return $payment;
        });
    }
}

if (!function_exists('completeCallback')) {
    function completeCallback($merchantRef, $gateway_details)
    {
        $payment = Payment::where('merchant_ref', $merchantRef)->with('payable')->first();
        $description = __("messages.payment_success_ad", ['title' => $payment->payable->title, 'amount' => $payment->amount]);
        return process_payment_callback($merchantRef, auth()->user()->id, $payment->amount, $payment->payable_id, $payment->type, $gateway_details, $description);
    }
}

if (!function_exists('checkWalletBalance')) {
    function checkWalletBalance($user, $price)
    {
        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
        if (!$wallet) {
            throw new \Exception(__("messages.wallet_not_found"));
        }
        $actualBalance = (float) $wallet->getRawOriginal('balance');
        if ($actualBalance < (float) $price) {
            throw new \Exception(__("messages.insufficient_balance"));
        }
        return true;
    }
}


if (!function_exists('incrementWallet')) {
    function incrementWallet($user, $amount)
    {
        $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
        if (!$wallet) {
            $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 0, 'reserved_balance' => 0]);
        }

        $oldBalance = $wallet->balance;
        $oldReserved = $wallet->reserved_balance;

        $wallet->balance += $amount;
        $wallet->reserved_balance += $amount;
        $wallet->save();

        WalletLog::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => PaymentType::WALLET_DEPOSIT->value,
            'balance_before' => $oldBalance,
            'balance_after' => $wallet->balance,
            'reserved_before' => $oldReserved,
            'reserved_after' => $wallet->reserved_balance,
            'reference' => PaymentType::WALLET_DEPOSIT->value,
            'description' => "Wallet Deposit",
        ]);
    }
}

if (!function_exists('decrementWallet')) {
    function decrementWallet(
        $user,
        $amount,
        $description = 'Wallet Withdraw'
    ) {
        $wallet = Wallet::where('user_id', $user->id)
            ->lockForUpdate()
            ->first();

        if (!$wallet) {
            throw new \Exception('Wallet not found');
        }

        $oldBalance = $wallet->balance;
        $oldReserved = $wallet->reserved_balance;

        $wallet->balance -= $amount;
        $wallet->save();

        WalletLog::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => PaymentType::WALLET_WITHDRAW->value,
            'balance_before' => $oldBalance,
            'balance_after' => $wallet->balance,
            'reserved_before' => $oldReserved,
            'reserved_after' => $wallet->reserved_balance,
            'reference' => PaymentType::WALLET_WITHDRAW->value,
            'description' => $description,
        ]);
    }
}

if (!function_exists('checkAuctionStatus')) {
    function checkAuctionStatus($auction)
    {
        if ($auction->status !== AuctionStatus::ACTIVE->value) {
            throw new \Exception(__("messages.auction_not_active"));
        }

        if ($auction->start_at->isFuture()) {
            throw new \Exception(__("messages.auction_not_started"));
        }

        if ($auction->end_at->isPast()) {
            throw new \Exception(__("messages.auction_ended"));
        }

        if ($auction->status === AuctionStatus::CANCELLED->value) {
            throw new \Exception(__("messages.auction_cancelled"));
        }
        return true;
    }
}


if (!function_exists('checkOwner')) {
    function checkOwner(int $user_id, int $object_id)
    {
        if ($user_id !== $object_id) {
            throw new \Exception(__("messages.unauthorized"));
        }
        return true;
    }
}

if (!function_exists('checkAvailableCard')) {
    function checkAvailableCard($user, string $card)
    {
        return DB::transaction(function () use ($user, $card) {
            $recharge = RechargeCard::where('card_number', $card)
                ->with('card')
                ->lockForUpdate()
                ->first();
            if (!$recharge || $recharge->used) {
                throw new \Exception("هذا الكارت غير صالح أو تم استخدامه مسبقاً.");
            }
            $recharge->update([
                'used' => true,
            ]);
            $amount = $recharge->card->recharge_amount;
            return $amount;
        });
    }
}



