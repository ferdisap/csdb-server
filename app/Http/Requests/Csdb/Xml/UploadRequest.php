<?php

namespace App\Http\Requests\Csdb\Xml;

use Closure;
use DOMDocument;
use Illuminate\Foundation\Http\FormRequest;

class UploadRequest extends FormRequest
{
    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $doc = new \DOMDocument();

        return [
            // tidak wajib karena ditentukan oleh isi file
            // "schema_type" => [
            //     function (string $attribute, mixed $value, Closure $fail) {
            //         $availableSchemaType = ["appliccrossreftableXsd", "brdocXsd", "brexXsd", "checklistXsd", "commentXsd", "comrepXsd", "condcrossreftableXsd", "containerXsd", "crewXsd", "dcXsd", "ddnXsd", "descriptXsd", "dmlXsd", "faultXsd", "frontmatterXsd", "icnmetadataXsd", "ipdXsd", "learningXsd", "pmXsd", "prdcrossreftableXsd", "procedXsd", "processXsd", "rdfXsd", "sbXsd", "schedulXsd", "scocontentXsd", "scormcontentpackageXsd", "updateXsd", "wrngdataXsd", "wrngfldsXsd", "xcfXsd"];
            //         if ($value && !in_array($value, $availableSchemaType)) {
            //             $fail("The {$attribute} is invalid.");
            //         };
            //     }
            // ],            
            // string WAJIB ada, text file xml
            "file" => [
                function (string $attribute, mixed $value, Closure $fail) use (&$doc) {
                    if (!$value) {
                        $fail("The {$attribute} is required.");
                        return;
                    }
                }
            ],
            // tidak wajib, string. Isinya schema file text <xs:schema>
            "schema_file" => [
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($value) {
                        $doc = new \DOMDocument();
                        $doc->loadXML($value);
                        if (!($doc->documentElement)) {
                            $fail("The {$attribute} is not valid form.");
                            return;
                        }
                    }
                }
            ],
            // tidak wajib. boolean. default = true
            "validate_schema" => [
                function (string $attribute, mixed $value, Closure $fail) use (&$doc) {
                    $doc->loadXML($value);
                    if ($doc->documentElement) {
                        if (!$value) {
                            libxml_use_internal_errors(true);
                            $value = $doc->documentElement->getAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'noNamespaceSchemaLocation');
                            if ($value) {
                                if (!$doc->schemaValidate($value)) {
                                    $fail("The csdb object is not valid form.");
                                    return;
                                }
                            } else {
                                $fail("The csdb object requires attribute 'noNamespaceSchemaLocation'.");
                            }
                        } 
                    }
                }
            ],
            // tidak wajib. boolean. default = true
            "validate_form" => [
                function (string $attribute, mixed $value, Closure $fail) use (&$doc) {
                    if ($value) {
                        $doc->loadXML($value);
                        if (!($doc->documentElement)) {
                            $fail("The csdb object is not valid form.");
                            return;
                        }
                    }
                }
            ],
            "force" => "boolean"
        ];
    }
}
