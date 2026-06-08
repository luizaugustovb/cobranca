<?php

namespace App\Services;

class ServerMonitoringService
{
    public function collect(): array
    {
        $cpu = $this->cpuMetrics();
        $memory = $this->memoryMetrics();
        $disk = $this->diskMetrics(base_path());
        $uptimeSeconds = $this->uptimeSeconds();

        return [
            'hostname' => gethostname() ?: php_uname('n'),
            'os' => php_uname('s') . ' ' . php_uname('r'),
            'php_version' => PHP_VERSION,
            'uptime_human' => $this->formatUptime($uptimeSeconds),
            'cpu' => $cpu,
            'memory' => $memory,
            'disk' => $disk,
        ];
    }

    private function cpuMetrics(): array
    {
        $loads = sys_getloadavg();
        $cores = $this->cpuCores();
        $load1m = isset($loads[0]) ? (float) $loads[0] : null;
        $usagePercent = null;

        if ($load1m !== null && $cores > 0) {
            $usagePercent = min(100, round(($load1m / $cores) * 100, 1));
        }

        return [
            'cores' => $cores,
            'load_1m' => $load1m,
            'load_5m' => isset($loads[1]) ? (float) $loads[1] : null,
            'load_15m' => isset($loads[2]) ? (float) $loads[2] : null,
            'usage_percent' => $usagePercent,
        ];
    }

    private function memoryMetrics(): array
    {
        $meminfoPath = '/proc/meminfo';

        if (!is_readable($meminfoPath)) {
            return [
                'total_bytes' => null,
                'used_bytes' => null,
                'free_bytes' => null,
                'usage_percent' => null,
                'total_label' => 'N/D',
                'used_label' => 'N/D',
                'free_label' => 'N/D',
            ];
        }

        $meminfo = file($meminfoPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $values = [];

        foreach ($meminfo as $line) {
            if (preg_match('/^(\w+):\s+(\d+)/', $line, $matches)) {
                $values[$matches[1]] = (int) $matches[2] * 1024;
            }
        }

        $total = $values['MemTotal'] ?? null;
        $available = $values['MemAvailable'] ?? ($values['MemFree'] ?? null);
        $used = ($total !== null && $available !== null) ? max(0, $total - $available) : null;
        $usagePercent = ($total && $used !== null) ? round(($used / $total) * 100, 1) : null;

        return [
            'total_bytes' => $total,
            'used_bytes' => $used,
            'free_bytes' => $available,
            'usage_percent' => $usagePercent,
            'total_label' => $this->formatBytes($total),
            'used_label' => $this->formatBytes($used),
            'free_label' => $this->formatBytes($available),
        ];
    }

    private function diskMetrics(string $path): array
    {
        $total = @disk_total_space($path) ?: null;
        $free = @disk_free_space($path) ?: null;
        $used = ($total !== null && $free !== null) ? max(0, $total - $free) : null;
        $usagePercent = ($total && $used !== null) ? round(($used / $total) * 100, 1) : null;

        return [
            'path' => $path,
            'total_bytes' => $total,
            'used_bytes' => $used,
            'free_bytes' => $free,
            'usage_percent' => $usagePercent,
            'total_label' => $this->formatBytes($total),
            'used_label' => $this->formatBytes($used),
            'free_label' => $this->formatBytes($free),
        ];
    }

    private function uptimeSeconds(): ?int
    {
        $uptimePath = '/proc/uptime';

        if (!is_readable($uptimePath)) {
            return null;
        }

        $content = trim((string) file_get_contents($uptimePath));
        $parts = preg_split('/\s+/', $content);

        return isset($parts[0]) ? (int) floor((float) $parts[0]) : null;
    }

    private function cpuCores(): int
    {
        $cpuinfoPath = '/proc/cpuinfo';

        if (is_readable($cpuinfoPath)) {
            preg_match_all('/^processor\s*:/m', (string) file_get_contents($cpuinfoPath), $matches);
            if (!empty($matches[0])) {
                return count($matches[0]);
            }
        }

        return 1;
    }

    private function formatBytes(?int $bytes): string
    {
        if ($bytes === null) {
            return 'N/D';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = (float) $bytes;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return number_format($size, $unitIndex === 0 ? 0 : 1, ',', '.') . ' ' . $units[$unitIndex];
    }

    private function formatUptime(?int $seconds): string
    {
        if ($seconds === null) {
            return 'N/D';
        }

        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        $parts = [];
        if ($days > 0) {
            $parts[] = $days . 'd';
        }
        if ($hours > 0) {
            $parts[] = $hours . 'h';
        }
        if ($minutes > 0 || empty($parts)) {
            $parts[] = $minutes . 'm';
        }

        return implode(' ', $parts);
    }
}
