<?php

use voku\helper\AntiXSS; // reff : https://github.com/voku/anti-xss

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

// SECURITY PLUGIN 

if (!function_exists('purify')) {
    function purify($post)
    {
        $antiXss = new AntiXSS();
        $antiXss->removeEvilAttributes(array('style')); // allow style-attributes
        return $antiXss->xss_clean($post);
    }
}

if (!function_exists('antiXss')) {
    function antiXss($data)
    {
        $antiXss = new AntiXSS();
        $antiXss->removeEvilAttributes(array('style')); // allow style-attributes

        $xssFound = false;
        if (is_array($data)) {
            foreach ($data as $post) {
                $antiXss->xss_clean($post);
                if ($antiXss->isXssFound()) {
                    $xssFound = true;
                }
            }
        } else {
            $antiXss->xss_clean($data);
            if ($antiXss->isXssFound()) {
                $xssFound = true;
            }
        }

        return $xssFound;
    }
}

// IMPORT EXCEL PLUGIN

if (!function_exists('readExcel')) {
    function readExcel($files, $filesPath, $maxAllowSize = 8388608)
    {
        $name = $files["name"];
        $tmp_name = $files["tmp_name"];
        $error = $files["error"];
        $size = $files["size"];
        $type = $files["type"];

        $allowedFileType = [
            'application/vnd.ms-excel',
            'text/xls',
            'text/xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        // 1st : check files type, only excel file are accepted
        if (in_array($type, $allowedFileType)) {

            // 2nd : check file size
            if ($size < $maxAllowSize) {
                if (file_exists($filesPath)) {

                    /**  Identify the type of $inputFileName  **/
                    $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filesPath);
                    /**  Create a new Reader of the type that has been identified  **/
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                    /**  Load $inputFileName to a Spreadsheet Object  **/
                    $spreadsheet = $reader->load($filesPath);
                    /**  Convert Spreadsheet Object to an Array for ease of use  **/
                    $spreadSheetAry = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

                    return ['code' => 201, 'data' => $spreadSheetAry, 'count' => count($spreadSheetAry)];
                } else {
                    return response(422, ['code' => 422, 'message' => 'The files upload was not found.']);
                }
            } else {
                return response(422, ['code' => 422, 'message' => 'The size is not supported : ' . $size . ' bytes']);
            }
        } else {
            return response(422, ['code' => 422, 'message' => 'The file type is not supported : ' . $type]);
        }
    }
}

// EXPORT EXCEL PLUGIN

if (!function_exists('exportToExcel')) {
    function exportToExcel($data, $filename = "data.xlsx", $option = NULL)
    {
        ini_set('display_errors', '1');
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0);

        try {
            // reset previous buffer
            ob_end_clean();

            // start output buffering
            ob_start();

            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();

            // set properties
            $title = empty($option) ? "My Excel Data" : (isset($option['title']) ? $option['title'] : "My Excel Data");
            $spreadsheet->getProperties()
                ->setTitle($title)
                ->setKeywords('data,export,excel')
                ->setCreator(APP_NAME)
                ->setCategory('Data Export')
                ->setCreated(timestamp());

            // Add data to the first sheet
            $sheet = $spreadsheet->getActiveSheet();

            // Set data in the worksheet
            $sheet->fromArray($data);

            // Set the headers to force a download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Create a new Xlsx writer and save the file
            $writer = new Xlsx($spreadsheet);

            // Check if the writer object is valid
            if ($writer === null) {
                return ['code' => 400, 'message' => 'Error creating Xlsx writer object'];
            }

            // end output buffering and flush the output
            ob_end_clean();

            $directory = 'public' . DIRECTORY_SEPARATOR . '_temp' . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR;
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $tempFile = $directory . 'export_excel.xls';
            if (file_exists($directory)) {
                unlink($tempFile);
            }

            $result = $writer->save($tempFile);

            // Save to computer.
            // $result = $writer->save('php://output');

            // Check if the file was saved successfully
            // if ($result === null) {
            // 	return ['code' => 400, 'message' => 'Error saving Excel file'];
            // 	exit;
            // }

            // Return success message
            return ['code' => 200, 'message' => 'File exported', 'filename' => $filename, 'path' => url($tempFile)];
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            return ['code' => 400, 'message' => 'Error writing to file: ', $e->getMessage()];
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return ['code' => 400, 'message' => 'Error: ', $e->getMessage()];
        } catch (Exception $e) {
            // Return error message
            return ['code' => 400, 'message' => 'Error exporting file: ' . $e->getMessage()];
        }
    }
}