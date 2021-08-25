2.0.0
    - Move clients into their own repository
    
1.0.1
    - Do not use deprecated ReactPHP class names

1.0.0
    - Add pan and zoom to dashboard charts
    - Rename anomaly scores HTTP resource

- 1.0.0-beta
    - Update to Rubix ML 1.0

- 0.4.0-beta
    - Only show chart snapshot on hover
    - Removed old static asset routes
    - Removed Encoder abstraction

- 0.2.9-beta
    - Fixed GraphQL long integer representation

- 0.2.8-beta
    - Web UI charts now driven by Plotly
    - Added snapshot feature to live charts
    - Move dataset visualizers to Visualizer package
    - Add append-only File logger

- 0.2.7-beta
    - Added static assets cache
    
- 0.2.6-beta
    - Added export chart feature to web UI
    - Added web UI automatic updates

- 0.2.5-beta
    - Added Backoff and Retry client middleware
    - Added server memory Circuit Breaker internal middleware
    - Added Line Chart dataset visualizer
    - Added service worker precache busting and navigation routing

- 0.2.4-beta
    - Added GraphQL API
    - Added dataset Bubble Chart visualizer
    - Dashboard memory chart now shows real memory usage
    - Removed Query Bus and associated application layer
    - Added inference rate chart to dashboard
    - Added Gzip compression of static assets

- 0.2.3-beta
    - Added web user interface
    - Added client middlewares
    - Added server dashboard view
    - Non 4xx error responses deferred for better concurrency
    - Rename internal command Response objects to Payload
    - Trusted Clients middleware now returns Forbidden (403) response
    - Removed single sample queries
    - Added Verbose interface for logger-aware servers
    - Removed RPC API and client
    - Renamed REST Server to HTTP Server
    - Adjustable server-sent events retry buffer
    - Support for Gzip and Deflate request body encodings
    - HTTP Server max concurrent requests now configurable
    - Allow REST client to disable SSL certificate verification

- 0.2.2-beta
    - Added Async Client interface
    - Added REST client
    - Added HTTP Access Log Generator middleware
    - Added command methods to client interfaces
    - Added Bzip2 message serializer
    - Optimized HTTP routing

- 0.2.1-beta
    - Support PSR-15 Server Request Handler interface
    - Implemented HTTP Basic Auth middleware
    - Added Gzip message serializer
    - Shared Token Authenticator uses bearer scheme and multiple tokens
    - Fix REST Server middleware stack
    - Added Response Time middleware
    - Added agent and response length to HTTP request logging
    - Removed Query Model and Server Status commands
    - RPC Client now uses Retry-After header on 429 and 503

- 0.2.0-beta
    - Update to Rubix ML 0.2.0
    - RPC client now has exponential backoff mechanism
    - Fixed JSON serialization of Estimator and Data types
    - Rename Rank and RankSample to Score and ScoreSample
    - Move Verbose interface to Server
    - Abstracted Router and Command Bus instantiation
    - Implemented Trusted Clients HTTP middleware

- 0.0.2-beta
    - Changed name of Binary serializer to Igbinary
    - Renamed server status requests per minute key

- 0.0.1-beta
    - Added REST and RPC Servers
    - Added RPC client with retry and backoff failover
    - Implemented Command Bus
    - Implemented messaging framework
    - Added Shared Token Authenticator HTTP middleware
    - Added REST and RPC controllers
    - Added predict single sample command
    - Added predict probabilities of a single sample
    - Added rank single sample command
    - Added Verbose interface for chatty servers
