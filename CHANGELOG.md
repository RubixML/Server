- 0.2.1-beta
    - Support PSR-15 Server Request Handler interface
    - Implemented HTTP Basic Auth middleware
    - Added Gzip message serializer
    - Shared Token Authenticator uses bearer scheme and multiple tokens

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
