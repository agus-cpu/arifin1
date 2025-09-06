<?php
// generate_hash.php

echo "Hash untuk admin123:\n";
echo password_hash('admin123', PASSWORD_DEFAULT);
echo "\n\n";

echo "Hash untuk owner123:\n";
echo password_hash('owner123', PASSWORD_DEFAULT);
echo "\n\n";

echo "Hash untuk customer123:\n";
echo password_hash('customer123', PASSWORD_DEFAULT);
echo "\n";