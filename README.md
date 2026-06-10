# Vercel PHP Deployment Guide

## Project Structure
```
vercel-project/
├── vercel.json          ← Vercel routing config
├── api/                 ← All PHP files (Vercel functions)
│   ├── index.php
│   ├── cart.php
│   └── ...all other .php files
├── database/
│   └── connection.php   ← Railway MySQL connection
└── setup_sessions_table.sql  ← Run this in Railway MySQL ONCE
```

## Step 1: Railway MySQL Setup
Run `setup_sessions_table.sql` in Railway MySQL dashboard

## Step 2: Vercel Environment Variables
Add these in Vercel Dashboard → Settings → Environment Variables:
```
DB_HOST     = your-railway-mysql-host
DB_USER     = root  
DB_PASS     = your-railway-password
DB_NAME     = railway (or your db name)
DB_PORT     = your-railway-port
```

## Step 3: assets/ folder
Vercel ma assets/ folder upload karo (images, css, js)
Ya to Cloudflare R2 use karo (recommended for large assets)

## Step 4: footer.php
footer.php pan api/ folder ma move karo
