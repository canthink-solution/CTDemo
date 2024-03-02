<?php

namespace Sys\framework;

/**
 * Validation Class
 *
 * @category  Form Input Validation
 * @package   Input
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   1.0.0
 */

class Validation
{
    /**
     * @var array Holds the data to be validated.
     */
    protected array $data;

    /**
     * @var array Holds the validation rules.
     */
    protected array $rules;

    /**
     * @var array Holds the validation results.
     */
    protected array $errors = [];

    /**
     * Validation constructor.
     *
     * @param array $data The data to be validated.
     * @param array|null $rules Optional. The validation rules.
     */
    public function __construct(array $data = [], ?array $rules = null)
    {
        $this->data = $data;
        $this->rules = $rules ?? [];
    }

    /**
     * Determine if an array is associative or not.
     *
     * @param array $array The array to check.
     * @return bool True if associative, false if not.
     */
    protected function isAssociative(array $array): bool
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }

    /**
     * Validate the provided data against the specified rules.
     *
     * @param array $data The data to be validated.
     * @param array $rules The validation rules.
     * @return bool True if validation passes, false otherwise.
     */
    public function validate(): bool
    {
        $this->errors = [];

        if ($this->isAssociative($this->data)) {
            // Data is associative, validate as single record
            return $this->validateRecord($this->data, $this->rules);
        } else {
            // Data is not associative, validate each record
            return $this->validateRecords($this->data, $this->rules);
        }
    }

    /**
     * Validate a single record against the specified rules.
     *
     * @param array $record The record to be validated.
     * @param array $rules The validation rules.
     * @return bool True if validation passes, false otherwise.
     */
    protected function validateRecord(array $record, array $rules): bool
    {
        foreach ($rules as $field => $rule) {
            $fieldData = $this->getFieldValue($field, $record);
            $fieldRules = explode('|', $rule);

            foreach ($fieldRules as $fieldRule) {
                list($ruleName, $params) = $this->parseRule($fieldRule);
                $method = 'validate' . ucfirst($ruleName);

                if (method_exists($this, $method)) {
                    // Check if the rule has the "required" condition
                    $isRequired = $this->ruleHasRequiredCondition($params);

                    // Perform validation even if the field value is empty or null and the rule is required
                    if ($isRequired || !$this->isEmptyOrNull($fieldData)) {
                        // Perform validation
                        $valid = $this->$method($fieldData, $params);
                        if (!$valid) {
                            $this->errors[] = $this->getErrorMessage($field, $ruleName, $params);
                        }
                    } else {
                        // Add error if the field is required but the value is empty or null
                        if ($ruleName === 'required') {
                            $this->errors[] = $this->getErrorMessage($field, $ruleName, $params);
                        }
                    }
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Check if a rule has the "required" condition.
     *
     * @param array $params The parameters of the rule.
     * @return bool True if the rule has the "required" condition, false otherwise.
     */
    protected function ruleHasRequiredCondition(array $params): bool
    {
        return in_array('required', $params);
    }

    /**
     * Check if a value is empty or null.
     *
     * @param mixed $value The value to check.
     * @return bool True if empty or null, false otherwise.
     */
    protected function isEmptyOrNull($value): bool
    {
        return $value === '' || $value === null;
    }

    /**
     * Validate an array of records against the specified rules.
     *
     * @param array $data The data to be validated.
     * @param array $rules The validation rules.
     * @return bool True if validation passes, false otherwise.
     */
    protected function validateRecords(array $data, array $rules): bool
    {
        $valid = true;

        foreach ($data as $index => $record) {
            if (!$this->validateRecord($record, $rules)) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Get the list of validation errors.
     *
     * @return array List of validation errors.
     */
    public function getError(): array
    {
        return ['code' => 400, 'message' => $this->errors];
    }

    /**
     * Parse the rule and extract rule name and parameters.
     *
     * @param string $rule The rule to parse.
     * @return array The rule name and parameters.
     */
    protected function parseRule(string $rule): array
    {
        $segments = explode(':', $rule, 2);
        $ruleName = $segments[0];
        $params = isset($segments[1]) ? explode(',', $segments[1]) : [];

        return [$ruleName, $params];
    }

    /**
     * Get the value of a field from the data array.
     *
     * @param string $field The field name.
     * @param array $data The data array.
     * @return mixed|null The value of the field.
     */
    protected function getFieldValue(string $field, array $data)
    {
        $keys = explode('.', $field);

        // Traverse through the nested array to retrieve the value
        foreach ($keys as $key) {
            if (!isset($data[$key])) {
                return null;
            }

            $data = $data[$key];
        }

        return $data;
    }

    // Validation rules implementation

    // Rule: Required
    protected function validateRequired($value): bool
    {
        return !empty($value);
    }

    // Rule: String
    protected function validateString($value): bool
    {
        return is_string($value);
    }

    // Rule: Integer
    protected function validateInteger($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    // Rule: Number
    protected function validateNumber($value): bool
    {
        return is_numeric($value);
    }

    // Rule: Email
    protected function validateEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    // Rule: Date
    protected function validateDate($value): bool
    {
        // Check if the date format is Y-m-d and it's not 0000-00-00
        if (strtotime($value) !== false && \DateTime::createFromFormat('Y-m-d', $value)->format('Y-m-d') === $value && $value !== '0000-00-00') {
            return true;
        }
        return false;
    }

    // Rule: Float
    protected function validateFloat($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    // Rule: Double (alias for Float)
    protected function validateDouble($value): bool
    {
        return $this->validateFloat($value);
    }

    // Rule: Min Length
    protected function validateMinLength($value, $params): bool
    {
        return strlen($value) >= $params[0];
    }

    // Rule: Max Length
    protected function validateMaxLength($value, $params): bool
    {
        return strlen($value) <= $params[0];
    }

    // Rule: File
    protected function validateFile($value): bool
    {
        return is_array($value) && isset($value['tmp_name']) && is_uploaded_file($value['tmp_name']);
    }

    // Rule: Max Size
    protected function validateMaxSize($value, $params): bool
    {
        $maxSize = $params[0] * 1024 * 1024; // Convert MB to bytes
        return isset($value['size']) && $value['size'] <= $maxSize;
    }

    // Rule: Mime
    protected function validateMime($value, $params): bool
    {
        if (!isset($value['tmp_name']) || !file_exists($value['tmp_name'])) {
            return false;
        }

        $allowedMimes = explode(',', $params[0]);
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($fileInfo, $value['tmp_name']);
        finfo_close($fileInfo);

        return in_array($mime, $allowedMimes);
    }

    // Rule: Required If
    protected function validateRequiredIf($value, $params): bool
    {
        [$field, $operator, $condition] = $params;
        $fieldValue = $this->getFieldValue($field, $this->data);

        // Conditional validation based on the operator and condition
        switch ($operator) {
            case '=':
                return $fieldValue == $condition;
            case '!=':
                return $fieldValue != $condition;
            case '<':
                return $fieldValue < $condition;
            case '<=':
                return $fieldValue <= $condition;
            case '>':
                return $fieldValue > $condition;
            case '>=':
                return $fieldValue >= $condition;
            default:
                return false;
        }
    }

    /**
     * Get the error message for a failed validation rule.
     *
     * @param string $field The field that failed validation.
     * @param string $ruleName The name of the validation rule that failed.
     * @param array $params The parameters of the validation rule.
     * @return string The error message.
     */
    protected function getErrorMessage(string $field, string $ruleName, array $params): string
    {
        switch ($ruleName) {
            case 'required':
                return "The $field field is required.";
            case 'string':
                return "The $field field must be a string.";
            case 'integer':
                return "The $field field must be an integer.";
            case 'number':
                return "The $field field must be a number.";
            case 'float':
                return "The $field field must be a float.";
            case 'double':
                return "The $field field must be a double.";
            case 'email':
                return "The $field field must be a valid email address.";
            case 'date':
                return "The $field field must be a valid date.";
            case 'minLength':
                return "The $field field must be at least {$params[0]} characters long.";
            case 'maxLength':
                return "The $field field must not exceed {$params[0]} characters.";
            case 'requiredIf':
                return "The $field field is required when {$params[0]} $params[1] {$params[2]}.";
            case 'file':
                return "The $field field must be a file.";
            case 'maxSize':
                return "The $field field exceeds the maximum size of {$params[0]} MB.";
            case 'mime':
                return "The $field field must be one of the following MIME types: " . implode(', ', $params);
            default:
                return "The $field field is invalid.";
        }
    }
}
