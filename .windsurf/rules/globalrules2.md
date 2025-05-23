---
trigger: always_on
---

## Security Headers
- Implement Content Security Policy
- Set X-Frame-Options
- Enable X-XSS-Protection
- Add X-Content-Type-Options
- Use Strict-Transport-Security

## API Versioning
- Version your API from the start
- Use URL versioning (e.g., `/api/v1/...`)
- Document breaking changes
- Provide migration guides between versions

## Caching Strategies
- Use cache tags for related cache items
- Implement cache invalidation strategies
- Use `remember()` for query caching
- Consider cache warming for critical paths

## Performance Monitoring
- Track slow queries
- Monitor memory usage
- Profile application performance
- Set up alerts for performance degradation

## Error Tracking
- Integrate with error tracking services
- Log all exceptions with context
- Set up error notifications
- Track and prioritize issues

## Database Best Practices
- Use migrations for schema changes
- Create seeders for test data
- Use database transactions
- Implement database backups
- Monitor query performance

## API Documentation
- Use OpenAPI/Swagger
- Document all endpoints
- Include request/response examples
- Document authentication methods
- Include error responses

## Testing Strategy
- Write unit tests for business logic
- Write feature tests for API endpoints
- Test edge cases
- Mock external services
- Use database transactions in tests

## Deployment Checklist
- Run tests
- Check environment configuration
- Run migrations
- Clear caches
- Restart queue workers
- Verify application health
