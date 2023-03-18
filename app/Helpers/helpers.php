<?php

use App\MediaUpload;
use App\StaticOption;

function check_image_extension($file)
{
    $extension = strtolower($file->getClientOriginalExtension());
    if ($extension != 'jpg' && $extension != 'jpeg' && $extension != 'png' && $extension = 'gif') {
        return false;
    }
    return true;
}



function get_attachment_image_by_id($id, $size = null, $default = false)
{
    $image_details = MediaUpload::find($id);
    $return_val = [];
    $image_url = '';

    if (file_exists('assets/uploads/media-uploader/' . optional($image_details)->path)) {
        $image_url = asset('assets/uploads/media-uploader/' . optional($image_details)->path);
    }

    if (!empty($id) && !empty($image_details)) {
        switch ($size) {
            case "large":
                if (file_exists('assets/uploads/media-uploader/large-' . $image_details->path)) {
                    $image_url = asset('assets/uploads/media-uploader/large-' . $image_details->path);
                }
                break;
            case "grid":
                if (file_exists('assets/uploads/media-uploader/grid-' . $image_details->path)) {
                    $image_url = asset('assets/uploads/media-uploader/grid-' . $image_details->path);
                }
                break;

            case "semi-large":
                if (file_exists('assets/uploads/media-uploader/semi-large-' . $image_details->path)) {
                    $image_url = asset('assets/uploads/media-uploader/semi-large-' . $image_details->path);
                }
                break;
            case "thumb":
                if (file_exists('assets/uploads/media-uploader/thumb-' . $image_details->path)) {
                    $image_url = asset('assets/uploads/media-uploader/thumb-' . $image_details->path);
                }else {
                    if (file_exists('assets/uploads/media-uploader/' . $image_details->path)) {
                        $image_url = asset('assets/uploads/media-uploader/' . $image_details->path);
                    }
                }
                break;
            default:
                if (file_exists('assets/uploads/media-uploader/' . $image_details->path)) {
                    $image_url = asset('assets/uploads/media-uploader/' . $image_details->path);
                }
                break;
        }
    }

    if (!empty($image_details)) {
        $return_val['image_id'] = $image_details->id;
        $return_val['path'] = $image_details->path;
        $return_val['img_url'] = $image_url;
        $return_val['img_alt'] = $image_details->alt;
    } elseif (empty($image_details) && $default) {
        $return_val['img_url'] = asset('assets/uploads/no-image.png');
    }

    return $return_val;
}


function render_image_markup_by_attachment_id($id, $class = null, $size = 'full')
{
    if (empty($id)) return '';
    $output = '';

    $image_details = get_attachment_image_by_id($id, $size);

    if (!empty($image_details)) {
        $class_list = !empty($class) ? 'class="' . $class . '"' : '';
        if(empty($image_details['img_url'])){
            return '';
        }

        $output = '<img src="' . $image_details['img_url'] . '" ' . $class_list . ' alt="' . $image_details['img_alt'] . '"/>';
    }
    return $output;
}

function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}


function set_static_option($key, $value)
{
    if (!StaticOption::where('option_name', $key)->first()) {
        StaticOption::create([
            'option_name' => $key,
            'option_value' => $value
        ]);
        return true;
    }
    return false;
}
function get_static_option($key,$default = null)
{
    $option_name = $key;
    $value = \Illuminate\Support\Facades\Cache::remember($option_name, 600, function () use($option_name) {
        return StaticOption::where('option_name', $option_name)->first();
    });

    return $value->option_value ?? $default;
}


function update_static_option($key, $value)
{
    if (!StaticOption::where('option_name', $key)->first()) {
        StaticOption::create([
            'option_name' => $key,
            'option_value' => $value
        ]);
        return true;
    } else {
        StaticOption::where('option_name', $key)->update([
            'option_name' => $key,
            'option_value' => $value
        ]);
        \Illuminate\Support\Facades\Cache::forget($key);
        return true;
    }
    return false;
}
function delete_static_option($key)
{
    \Illuminate\Support\Facades\Cache::forget($key);
    return (boolean) StaticOption::where('option_name', $key)->delete();
}