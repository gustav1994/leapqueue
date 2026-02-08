# LeapQueue 🐸 ➔ 🚀

LeapQueue is a high-throughput, database-backed job queue for PHP and MySQL. It is designed to handle massive workloads using standard infrastructure, providing a high-speed alternative to external brokers like Redis or RabbitMQ without adding new dependencies to your stack.

## The "Boring IT" Philosophy

LeapQueue is built on the principle of **"Choosing Boring Technology."** While modern software architecture often leans toward complex microservices and external message brokers, MySQL remains one of the most stable, predictable, and well-understood components of the LAMP stack.

By leveraging advanced MySQL features LeapQueue transforms a standard database table into a high-performance engine. This allows developers to maintain a simple, reliable architecture while achieving enterprise-grade throughput.

## Use Cases

LeapQueue is optimized for scenarios where processing speed and infrastructure simplicity are paramount.

* **Mass Marketing Communications:** Efficiently dispatching large-scale email or SMS campaigns without overwhelming the database.
* **Data Migrations & Synchronization:** Handling heavy ETL (Extract, Transform, Load) tasks or syncing large datasets between external APIs and local storage.
* **Resource-Intensive Background Tasks:** Offloading image processing, PDF generation, or complex report calculations to background workers.
* **General Purpose Queuing:** Replacing standard cron-based processing for everyday background tasks like cache warming or webhook handling.

## Comparative Advantage

LeapQueue is designed to fill the performance gap between traditional scheduling tools and general-purpose framework queues.

### LeapQueue vs. ActionScheduler (WordPress)
ActionScheduler is the standard for WordPress, but it is optimized for scheduling and administrative reliability. When the requirement shifts to raw processing power and extreme volumes, ActionScheduler's metadata overhead can become a bottleneck. LeapQueue serves as a "high-speed lane," designed to process the queue as fast as the hardware allows.

### LeapQueue vs. Laravel Queue (Database)
While Laravel’s database driver is excellent for general use, LeapQueue provides several advanced features not supported natively by the Laravel database driver:

* **Multi-Job Batching:** Laravel typically processes one job per bootstrap cycle. LeapQueue can pull and process hundreds of jobs in a single batch, drastically reducing the "bootstrap tax" and increasing throughput for small tasks.
* **Grouped FIFO:** LeapQueue allows for sequential processing within a specific group (e.g., `user_id`), ensuring that tasks for a single entity are handled in order while the rest of the queue remains highly parallel.
* **Advanced Time-Based Retries:** Sophisticated retry logic with precise time-based backoffs, allowing for granular control over failed tasks without blocking the rest of the engine.

## Integration and Scaling

LeapQueue is framework-agnostic and requires only a PDO connection. It is built to be easily integrated into WordPress plugins, Laravel applications, or standalone PHP packages.

### WordPress Integration
LeapQueue can be used alongside ActionScheduler. You can keep ActionScheduler for your daily cron tasks while offloading massive data migrations or broadcast emails to LeapQueue for significantly higher performance.

### Concurrent Workers
The architecture supports horizontal scaling. You can launch multiple parallel worker processes across different servers or containers without causing database deadlocks or performance degradation, thanks to the non-blocking nature of its selection logic.

## Performance and Stability

The engine utilizes a self-regulating strategy to ensure stability. Instead of using static batch sizes, it calculates the optimal workload by analyzing previous execution times and memory usage. This ensures the worker always operates within the safe limits of the environment, regardless of the underlying hardware.

## Benchmarks

Detailed performance metrics and stress-test results are added here as testing continues. These benchmarks focus on Jobs-Per-Second (JPS) rates and system stability under varying levels of concurrency.

*(Benchmark results will be populated following environment testing)*

## License

MIT License.

---

## Fil- og Folderstruktur

Projektet er organiseret således:

```
/src
	/Drivers      -> Database drivers (fx GenericDriver, MySqlDriver, etc.)
	/Strategies   -> Batch/optimeringslogik (fx AdaptiveEmaStrategy)
	/Workers      -> Arbejdertyper (fx CliWorker, LoopbackWorker, WordPressWorker)
	Manager.php   -> Hoved-API
/database       -> schema.sql (jobs, health)
sandbox/        -> seed.php, benchmark.php, monitor.php
docker/         -> Dockerfile, docker-compose.yml
LICENSE
readme.md
composer.json   -> Dependency og autoloading
.env.example    -> Database config eksempel
```

Mapperne under `/src` bør være Capital case (PascalCase) for at matche PSR-4 og PHP-namespace konventioner.