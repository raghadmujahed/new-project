<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class FeatureFlagController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(FeatureFlag::class, 'feature_flag');
    }

    public function index(Request $request)
    {
        ActivityLogger::log(
            'feature_flag',
            'view_list',
            'Opened Feature Flags page',
            null,
            [],
            $request->user()
        );

        $flags = FeatureFlag::all();

        ActivityLogger::log(
            'feature_flag',
            'view_data',
            'Fetched all feature flags',
            null,
            ['count' => $flags->count()],
            $request->user()
        );

        return response()->json($flags);
    }

    public function update(Request $request, FeatureFlag $featureFlag)
    {
        $request->validate([
            'is_open' => 'required|boolean'
        ]);

        $oldValue = $featureFlag->is_open;

        $featureFlag->update([
            'is_open' => $request->is_open
        ]);

        ActivityLogger::log(
            'feature_flag',
            'updated',
            'Updated feature flag',
            $featureFlag,
            [
                'flag_name' => $featureFlag->name,
                'old_value' => $oldValue,
                'new_value' => $request->is_open
            ],
            $request->user()
        );

        return response()->json($featureFlag);
    }

    public function check($name, Request $request)
    {
        $flag = FeatureFlag::where('name', $name)->first();

        ActivityLogger::log(
            'feature_flag',
            'check',
            'Checked feature flag',
            null,
            [
                'flag_name' => $name,
                'is_open' => $flag?->is_open ?? false
            ],
            $request->user()
        );

        return response()->json([
            'name' => $name,
            'is_open' => $flag?->is_open ?? false
        ]);
    }
}