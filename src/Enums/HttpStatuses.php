<?php

namespace InfinityXTech\FilamentApiDocsBuilder\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum HttpStatuses: int implements HasLabel, HasColor, HasIcon
{
    // 1xx Informational Responses
    case CONTINUE = 100;
    case SWITCHING_PROTOCOLS = 101;
    case PROCESSING = 102;
    case EARLY_HINTS = 103;

    // 2xx Success Responses
    case OK = 200;
    case CREATED = 201;
    case ACCEPTED = 202;
    case NON_AUTHORITATIVE_INFORMATION = 203;
    case NO_CONTENT = 204;
    case RESET_CONTENT = 205;
    case PARTIAL_CONTENT = 206;
    case MULTI_STATUS = 207;
    case ALREADY_REPORTED = 208;
    case IM_USED = 226;

    // 3xx Redirection Responses
    case MULTIPLE_CHOICES = 300;
    case MOVED_PERMANENTLY = 301;
    case FOUND = 302;
    case SEE_OTHER = 303;
    case NOT_MODIFIED = 304;
    case USE_PROXY = 305;
    case UNUSED = 306; // This status code is no longer used but reserved.
    case TEMPORARY_REDIRECT = 307;
    case PERMANENT_REDIRECT = 308;

    // 4xx Client Error Responses
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case NOT_ACCEPTABLE = 406;
    case PROXY_AUTHENTICATION_REQUIRED = 407;
    case REQUEST_TIMEOUT = 408;
    case CONFLICT = 409;
    case GONE = 410;
    case LENGTH_REQUIRED = 411;
    case PRECONDITION_FAILED = 412;
    case PAYLOAD_TOO_LARGE = 413;
    case URI_TOO_LONG = 414;
    case UNSUPPORTED_MEDIA_TYPE = 415;
    case RANGE_NOT_SATISFIABLE = 416;
    case EXPECTATION_FAILED = 417;
    case IM_A_TEAPOT = 418;
    case MISDIRECTED_REQUEST = 421;
    case UNPROCESSABLE_ENTITY = 422;
    case LOCKED = 423;
    case FAILED_DEPENDENCY = 424;
    case TOO_EARLY = 425;
    case UPGRADE_REQUIRED = 426;
    case PRECONDITION_REQUIRED = 428;
    case TOO_MANY_REQUESTS = 429;
    case REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    case UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    // 5xx Server Error Responses
    case INTERNAL_SERVER_ERROR = 500;
    case NOT_IMPLEMENTED = 501;
    case BAD_GATEWAY = 502;
    case SERVICE_UNAVAILABLE = 503;
    case GATEWAY_TIMEOUT = 504;
    case HTTP_VERSION_NOT_SUPPORTED = 505;
    case VARIANT_ALSO_NEGOTIATES = 506;
    case INSUFFICIENT_STORAGE = 507;
    case LOOP_DETECTED = 508;
    case NOT_EXTENDED = 510;
    case NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * Get the title of the HTTP status code.
     *
     * @return string
     */
    public function getLabel(): ?string
    {
        return match ($this) {
            // 1xx Informational
            self::CONTINUE => '100 Continue',
            self::SWITCHING_PROTOCOLS => '101 Switching Protocols',
            self::PROCESSING => '102 Processing',
            self::EARLY_HINTS => '103 Early Hints',

            // 2xx Success
            self::OK => '200 OK',
            self::CREATED => '201 Created',
            self::ACCEPTED => '202 Accepted',
            self::NON_AUTHORITATIVE_INFORMATION => '203 Non-Authoritative Information',
            self::NO_CONTENT => '204 No Content',
            self::RESET_CONTENT => '205 Reset Content',
            self::PARTIAL_CONTENT => '206 Partial Content',
            self::MULTI_STATUS => '207 Multi-Status',
            self::ALREADY_REPORTED => '208 Already Reported',
            self::IM_USED => '226 IM Used',

            // 3xx Redirection
            self::MULTIPLE_CHOICES => '300 Multiple Choices',
            self::MOVED_PERMANENTLY => '301 Moved Permanently',
            self::FOUND => '302 Found',
            self::SEE_OTHER => '303 See Other',
            self::NOT_MODIFIED => '304 Not Modified',
            self::USE_PROXY => '305 Use Proxy',
            self::UNUSED => '306 (Unused)',
            self::TEMPORARY_REDIRECT => '307 Temporary Redirect',
            self::PERMANENT_REDIRECT => '308 Permanent Redirect',

            // 4xx Client Errors
            self::BAD_REQUEST => '400 Bad Request',
            self::UNAUTHORIZED => '401 Unauthorized',
            self::PAYMENT_REQUIRED => '402 Payment Required',
            self::FORBIDDEN => '403 Forbidden',
            self::NOT_FOUND => '404 Not Found',
            self::METHOD_NOT_ALLOWED => '405 Method Not Allowed',
            self::NOT_ACCEPTABLE => '406 Not Acceptable',
            self::PROXY_AUTHENTICATION_REQUIRED => '407 Proxy Authentication Required',
            self::REQUEST_TIMEOUT => '408 Request Timeout',
            self::CONFLICT => '409 Conflict',
            self::GONE => '410 Gone',
            self::LENGTH_REQUIRED => '411 Length Required',
            self::PRECONDITION_FAILED => '412 Precondition Failed',
            self::PAYLOAD_TOO_LARGE => '413 Payload Too Large',
            self::URI_TOO_LONG => '414 URI Too Long',
            self::UNSUPPORTED_MEDIA_TYPE => '415 Unsupported Media Type',
            self::RANGE_NOT_SATISFIABLE => '416 Range Not Satisfiable',
            self::EXPECTATION_FAILED => '417 Expectation Failed',
            self::IM_A_TEAPOT => '418 I\'m a Teapot',
            self::MISDIRECTED_REQUEST => '421 Misdirected Request',
            self::UNPROCESSABLE_ENTITY => '422 Unprocessable Entity',
            self::LOCKED => '423 Locked',
            self::FAILED_DEPENDENCY => '424 Failed Dependency',
            self::TOO_EARLY => '425 Too Early',
            self::UPGRADE_REQUIRED => '426 Upgrade Required',
            self::PRECONDITION_REQUIRED => '428 Precondition Required',
            self::TOO_MANY_REQUESTS => '429 Too Many Requests',
            self::REQUEST_HEADER_FIELDS_TOO_LARGE => '431 Request Header Fields Too Large',
            self::UNAVAILABLE_FOR_LEGAL_REASONS => '451 Unavailable For Legal Reasons',

            // 5xx Server Errors
            self::INTERNAL_SERVER_ERROR => '500 Internal Server Error',
            self::NOT_IMPLEMENTED => '501 Not Implemented',
            self::BAD_GATEWAY => '502 Bad Gateway',
            self::SERVICE_UNAVAILABLE => '503 Service Unavailable',
            self::GATEWAY_TIMEOUT => '504 Gateway Timeout',
            self::HTTP_VERSION_NOT_SUPPORTED => '505 HTTP Version Not Supported',
            self::VARIANT_ALSO_NEGOTIATES => '506 Variant Also Negotiates',
            self::INSUFFICIENT_STORAGE => '507 Insufficient Storage',
            self::LOOP_DETECTED => '508 Loop Detected',
            self::NOT_EXTENDED => '510 Not Extended',
            self::NETWORK_AUTHENTICATION_REQUIRED => '511 Network Authentication Required',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            // 1xx Informational
            self::CONTINUE, self::SWITCHING_PROTOCOLS, self::PROCESSING, self::EARLY_HINTS => 'sky',

            // 2xx Success
            self::OK, self::CREATED, self::ACCEPTED, self::NON_AUTHORITATIVE_INFORMATION, self::NO_CONTENT,
            self::RESET_CONTENT, self::PARTIAL_CONTENT, self::MULTI_STATUS, self::ALREADY_REPORTED, self::IM_USED => 'teal',

            // 3xx Redirection
            self::MULTIPLE_CHOICES, self::MOVED_PERMANENTLY, self::FOUND, self::SEE_OTHER, self::NOT_MODIFIED,
            self::USE_PROXY, self::UNUSED, self::TEMPORARY_REDIRECT, self::PERMANENT_REDIRECT => 'orange',

            // 4xx Client Errors
            self::BAD_REQUEST, self::UNAUTHORIZED, self::PAYMENT_REQUIRED, self::FORBIDDEN, self::NOT_FOUND,
            self::METHOD_NOT_ALLOWED, self::NOT_ACCEPTABLE, self::PROXY_AUTHENTICATION_REQUIRED, self::REQUEST_TIMEOUT,
            self::CONFLICT, self::GONE, self::LENGTH_REQUIRED, self::PRECONDITION_FAILED, self::PAYLOAD_TOO_LARGE,
            self::URI_TOO_LONG, self::UNSUPPORTED_MEDIA_TYPE, self::RANGE_NOT_SATISFIABLE, self::EXPECTATION_FAILED,
            self::IM_A_TEAPOT, self::MISDIRECTED_REQUEST, self::UNPROCESSABLE_ENTITY, self::LOCKED, self::FAILED_DEPENDENCY,
            self::TOO_EARLY, self::UPGRADE_REQUIRED, self::PRECONDITION_REQUIRED, self::TOO_MANY_REQUESTS,
            self::REQUEST_HEADER_FIELDS_TOO_LARGE, self::UNAVAILABLE_FOR_LEGAL_REASONS => 'red',

            // 5xx Server Errors
            self::INTERNAL_SERVER_ERROR, self::NOT_IMPLEMENTED, self::BAD_GATEWAY, self::SERVICE_UNAVAILABLE,
            self::GATEWAY_TIMEOUT, self::HTTP_VERSION_NOT_SUPPORTED, self::VARIANT_ALSO_NEGOTIATES,
            self::INSUFFICIENT_STORAGE, self::LOOP_DETECTED, self::NOT_EXTENDED, self::NETWORK_AUTHENTICATION_REQUIRED => 'red',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            // 1xx Informational
            self::CONTINUE, self::SWITCHING_PROTOCOLS, self::PROCESSING, self::EARLY_HINTS => 'heroicon-o-information-circle',

            // 2xx Success
            self::OK, self::CREATED, self::ACCEPTED, self::NON_AUTHORITATIVE_INFORMATION, self::NO_CONTENT,
            self::RESET_CONTENT, self::PARTIAL_CONTENT, self::MULTI_STATUS, self::ALREADY_REPORTED, self::IM_USED => 'heroicon-o-check-circle',

            // 3xx Redirection
            self::MULTIPLE_CHOICES, self::MOVED_PERMANENTLY, self::FOUND, self::SEE_OTHER, self::NOT_MODIFIED,
            self::USE_PROXY, self::UNUSED, self::TEMPORARY_REDIRECT, self::PERMANENT_REDIRECT => 'heroicon-o-arrow-circle-right',

            // 4xx Client Errors
            self::BAD_REQUEST, self::UNAUTHORIZED, self::PAYMENT_REQUIRED, self::FORBIDDEN, self::NOT_FOUND,
            self::METHOD_NOT_ALLOWED, self::NOT_ACCEPTABLE, self::PROXY_AUTHENTICATION_REQUIRED, self::REQUEST_TIMEOUT,
            self::CONFLICT, self::GONE, self::LENGTH_REQUIRED, self::PRECONDITION_FAILED, self::PAYLOAD_TOO_LARGE,
            self::URI_TOO_LONG, self::UNSUPPORTED_MEDIA_TYPE, self::RANGE_NOT_SATISFIABLE, self::EXPECTATION_FAILED,
            self::IM_A_TEAPOT, self::MISDIRECTED_REQUEST, self::UNPROCESSABLE_ENTITY, self::LOCKED, self::FAILED_DEPENDENCY,
            self::TOO_EARLY, self::UPGRADE_REQUIRED, self::PRECONDITION_REQUIRED, self::TOO_MANY_REQUESTS,
            self::REQUEST_HEADER_FIELDS_TOO_LARGE, self::UNAVAILABLE_FOR_LEGAL_REASONS => 'heroicon-o-exclamation-triangle',

            // 5xx Server Errors
            self::INTERNAL_SERVER_ERROR, self::NOT_IMPLEMENTED, self::BAD_GATEWAY, self::SERVICE_UNAVAILABLE,
            self::GATEWAY_TIMEOUT, self::HTTP_VERSION_NOT_SUPPORTED, self::VARIANT_ALSO_NEGOTIATES,
            self::INSUFFICIENT_STORAGE, self::LOOP_DETECTED, self::NOT_EXTENDED, self::NETWORK_AUTHENTICATION_REQUIRED => 'heroicon-o-x-circle',
        };
    }
}
