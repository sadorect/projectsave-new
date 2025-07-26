# S3 Setup for LMS Files

## Configuration Summary

The Laravel application is now configured to store LMS files in Amazon S3 with the following structure:

```
S3 Bucket: casperit-bucket
Root Folder: projectsave/
```

## File Organization

### LMS Course Files
- **Course Images**: `projectsave/lms/courses/images/`
- **Course Documents**: `projectsave/lms/courses/documents/`

### File Types Supported
- **Images**: jpeg, png, jpg, gif, webp (max 2MB)
- **Documents**: pdf, doc, docx, xls, xlsx, ppt, pptx (max 10MB)

## Controllers Updated

1. **AdminCourseController** - `/app/Http/Controllers/Admin/AdminCourseController.php`
   - Store and update methods updated for S3
   - Proper file deletion handling for S3

2. **LMS CourseController** - `/app/Http/Controllers/LMS/CourseController.php`
   - Store and update methods updated for S3
   - Proper file deletion handling for S3

## Environment Configuration

Required `.env` variables:
```bash
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=[your-access-key]
AWS_SECRET_ACCESS_KEY=[your-secret-key]
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=casperit-bucket
AWS_USE_PATH_STYLE_ENDPOINT=false
AWS_URL=https://casperit-bucket.s3.us-east-1.amazonaws.com
```

## S3 Bucket Configuration

The S3 configuration in `config/filesystems.php` includes:
- Root folder: `projectsave`
- Visibility: `public` (for direct access to files)
- Proper error handling with `throw: false`

## File Access

Files stored in S3 will be accessible via direct URLs:
```
https://casperit-bucket.s3.us-east-1.amazonaws.com/projectsave/lms/courses/images/filename.jpg
https://casperit-bucket.s3.us-east-1.amazonaws.com/projectsave/lms/courses/documents/filename.pdf
```

## Testing

To test the S3 integration:
1. Upload a course with featured image
2. Upload course documents
3. Verify files appear in S3 bucket under `projectsave/lms/` folder
4. Verify files are accessible via the generated URLs

## Views Updated for S3

The following LMS views have been updated to properly display S3 images with fallback support:

1. **Course Listing** - `/resources/views/lms/courses/index.blade.php`
2. **Course Details** - `/resources/views/lms/courses/show.blade.php`
3. **Course Landing** - `/resources/views/lms/courses/landing.blade.php`
4. **LMS Dashboard** - `/resources/views/lms/dashboard/index.blade.php`
5. **Admin Course View** - `/resources/views/admin/courses/show.blade.php`
6. **Admin Course Form** - `/resources/views/admin/courses/form.blade.php`

## Image Fallback System

All course image displays now include:
- Error handling with `onerror` attribute
- Fallback to placeholder image: `/public/frontend/img/course-placeholder.jpg`
- Graceful handling of missing or broken S3 URLs

## Course Model Enhancements

Added helper methods to `App\Models\Course`:
- `getFeaturedImageUrlAttribute()` - Returns image URL with fallback
- `hasFeaturedImage()` - Checks if course has valid image

## Migration Tools

### Artisan Command
```bash
# Check what would be migrated (dry run)
php artisan courses:migrate-images-to-s3 --dry-run

# Execute the migration
php artisan courses:migrate-images-to-s3
```

### Database Migration
Run the migration to update existing local paths:
```bash
php artisan migrate
```

## Testing S3 Integration

1. Upload a new course with featured image
2. Verify file appears in S3: `projectsave/lms/courses/images/`
3. Check course displays correctly in all views
4. Test fallback by temporarily breaking S3 URL

## Next Steps

For additional file types (posts, events, avatars), similar updates can be made to their respective controllers using the same pattern.
