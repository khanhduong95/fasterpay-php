<?php

namespace FasterPay\Response;

class JsonResponse extends GeneralResponse
{
    public function handleResult()
    {
        $response = $this->getDecodeResponse();
        $httpCode = $this->getHttpCode();

        // First check HTTP status
        if ($httpCode != self::SUCCESS_CODE) {
            $errorMessage = empty($response['message']) ? self::RESPONSE_ERROR_TEXT : $response['message'];
            $this->errors = new ResponseError([
                'message' => $errorMessage,
                'code' => $httpCode
            ]);
            return;
        }

        // Then check if response was successfully decoded
        if ($response === null) {
            $this->errors = new ResponseError([
                'message' => 'Failed to decode JSON response',
                'code' => self::DEFAULT_ERROR_CODE
            ]);
            return;
        }

        // Check for success flag in response body
        if (!empty($response['success'])) {
            $this->success = true;
        } else {
            // Response decoded but success is empty/false/missing
            $errorMessage = isset($response['message']) ? $response['message'] : 'Request was not successful';
            $this->errors = new ResponseError([
                'message' => $errorMessage,
                'code' => isset($response['code']) ? $response['code'] : self::DEFAULT_ERROR_CODE
            ]);
        }
    }

    public function isSuccessful()
    {
        return $this->success === true && empty($this->errors);
    }
}