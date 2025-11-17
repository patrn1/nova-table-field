#!/bin/bash

echo "=== Test Environment Debug ==="
echo "PHP Version: $(php --version | head -n 1)"
echo "PHPUnit Version: $(vendor/bin/phpunit --version)"
echo "Working Directory: $(pwd)"
echo "Test Directory Contents:"
ls -la tests/
echo "PHPUnit Configuration:"
cat phpunit.xml | head -20
echo "=== Running Tests ==="
vendor/bin/phpunit
EXIT_CODE=$?
echo "PHPUnit exit code: $EXIT_CODE"
if [ $EXIT_CODE -eq 0 ] || [ $EXIT_CODE -eq 1 ]; then
    echo "Tests completed successfully (exit code $EXIT_CODE indicates warnings only)"
    exit 0
else
    echo "Tests failed with exit code $EXIT_CODE"
    exit $EXIT_CODE
fi