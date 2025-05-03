<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Enums\ViewPaths\Admin\AddonSetup;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AddonRequest;
use App\Services\AddonService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class AddonController extends BaseController
{

    /**
     * @param Request|null $request
     * @param string|null $type
     * @return View Index function is the starting point of a controller
     * Index function is the starting point of a controller
     */
    public function index(Request|null $request, string $type = null): View
    {
        return $this->getView();
    }

    public function getView(): View
    {
        $addons = self::getDirectories();
        return view(AddonSetup::VIEW[VIEW], compact('addons'));
    }

    public function publish(Request $request, AddonService $addonService): JsonResponse|int
    {
        $data = $addonService->getPublishData(request: $request);
        return response()->json($data);
    }

    public function activation(Request $request): Redirector | RedirectResponse | Application
    {
        $remove = ["http://", "https://", "www."];
        $url = str_replace($remove, "", url('/'));
        $full_data = include $request['path'] . '/Addon/info.php';
        $full_data['is_published'] = 1;
        $full_data['username'] = $request['username'];
        $full_data['purchase_code'] = $request['purchase_code'];
        $str = "<?php return " . var_export($full_data, true) . ";";
        file_put_contents(base_path($request['path'] . '/Addon/info.php'), $str);
        Toastr::success(translate('activated_successfully'));
        return back();
    }

    public function upload(AddonRequest $request, AddonService $addonService): JsonResponse
    {
        $data = $addonService->getUploadData(request: $request);
        return response()->json([
            'status' => $data['status'],
            'message'=> $data['message']
        ]);
    }

    public function delete(Request $request, AddonService $addonService): JsonResponse
    {
        $data = $addonService->deleteAddon(request: $request);
        return response()->json($data);
    }

    function getDirectories(): array
    {
        $scan = scandir(base_path('Modules/'));
        $addonsFolders = array_diff($scan, ['.', '..','.DS_Store']);
        $addons = [];
        foreach ($addonsFolders as $directory) {
            $addons[] = 'Modules/' . $directory;
        }
        return $addons;
    }
}
