<?php

$curpath = "/wisata_kaliurang";

define("INIT", true);

$httpCode = 200;
$data = [];

require __DIR__."/helpers.php";

if (isset($_GET["action"])) {
	switch ($_GET["action"]) {
		case "thumbnail":
			if (
				isset($_GET["file"]) &&
				is_string($_GET["file"]) &&
				($_GET["file"] = basename($_GET["file"])) &&
				file_exists($_GET["file"] = __DIR__."/files/".$_GET["file"])
			) {
				$allowedExt = ["jpg"];

				$ext = explode(".", $_GET["file"]);
				$ext = count($ext) > 1 ? strtolower(end($ext)) : null;

				if (in_array($ext, $allowedExt)) {
					session_cache_limiter("none");

					$expire = 60 * 60 * 24 * 365;

					header("Cache-control: max-age=".$expire);
					header("Expires: ".gmdate(DATE_RFC1123 ,time()+$expire));
					header("Last-Modified: ".gmdate(DATE_RFC1123, filemtime($_GET["file"])));

					// Check if page has changed. If not, send 304 and exit
					if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
						header("HTTP/1.1 304 Not Modified");
						exit;
					}

					header("Content-Type: image/jpeg");

					$cache_file = $_GET["file"]."._cache";
					if (file_exists($cache_file)) {
						readfile($cache_file);
					} else {
						ob_start();
						$res = resize_image($_GET["file"], 500, 400, false);
						imagejpeg($res);
						$data = ob_get_clean();
						file_put_contents($cache_file, $data);
						echo $data;
					}
					exit;
				}
			}
			break;

		case "fetch":
			$limit = 20;
			$page = (isset($_GET["page"]) && is_numeric($_GET["page"])) ? (int)$_GET["page"] : 1;
			$start = $limit * ($page - 1);
			$end = $start + $limit - 1;

			$scan = glob(__DIR__."/files/*.JPG");

			$allowedExt = ["jpg"];
			$data = ["status_code" => 200, "data" => []];
			foreach ($scan as $k => &$v) {
				if (($k >= $start) && ($k <= $end)) {
					$v = basename($v);
					$ext = explode(".", $v);
					$ext = count($ext) > 1 ? strtolower(end($ext)) : null;
					if (in_array($ext, $allowedExt)) {
						$f = explode(".", $v);
						unset($f[count($f) - 1]);
						$f = __DIR__."/files/".implode(".", $f).".CR2";
						$data["data"][] = [
							"name" => $v,
							"has_cr" => file_exists($f)
						];
					}
				}
			}
			goto json_res;
			break;

		case "download":
			if (isset($_GET["type"]) && isset($_GET["file"]) && is_string($_GET["file"])) {
				$_GET["file"] = basename($_GET["file"]);
				if ($_GET["type"] === "cr2") {
					$f = explode(".", $_GET["file"]);
					unset($f[count($f) - 1]);
					$f = __DIR__."/files/".implode(".", $f).".CR2";
					if (file_exists($f)) {
						goto download_file;
					}
				} else if ($_GET["type"] === "jpg") {
					$f = __DIR__."/files/".$_GET["file"];
					if (file_exists($f)) {
						goto download_file;
					}
				}
			}
			break;

		default:
			break;
	}
}


$httpCode = 400;
$data = ["status_code" => 400, "error_msg" => "Bad Request"];

json_res:
header("Content-Type: application/json");
http_response_code($httpCode);
print json_encode($data, JSON_UNESCAPED_SLASHES);
exit;

download_file:
header("Content-Description: File Transfer");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"".basename($f)."\"");
header("Expires: 0");
header("Cache-Control: must-revalidate");
header("Pragma: public");
header("Content-Length: ".filesize($f));
flush();
readfile($f);
