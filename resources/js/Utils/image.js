/**
 * Compress and resize an image file using standard HTML5 Canvas.
 * Resizes the image so that its longest side is at most maxSide,
 * and encodes it as JPEG with the specified quality (0.0 to 1.0).
 * 
 * @param {File} file The original File object
 * @param {number} maxSide Maximum width/height of the output image
 * @param {number} quality JPEG compression quality (0.0 to 1.0)
 * @returns {Promise<File>} A Promise that resolves to the compressed File object
 */
export function compressImage(file, maxSide = 300, quality = 0.8) {
  return new Promise((resolve) => {
    // If it's not an image, return the original file
    if (!file || !file.type.startsWith('image/')) {
      resolve(file);
      return;
    }

    const reader = new FileReader();
    reader.onload = (e) => {
      const img = new Image();
      img.onload = () => {
        let width = img.width;
        let height = img.height;

        // Calculate new dimensions preserving aspect ratio
        if (width > maxSide || height > maxSide) {
          if (width > height) {
            height = Math.round((height * maxSide) / width);
            width = maxSide;
          } else {
            width = Math.round((width * maxSide) / height);
            height = maxSide;
          }
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, width, height);

        // Convert to blob using image/jpeg for maximum compatibility
        canvas.toBlob((blob) => {
          if (blob) {
            // Replace extension with .jpg
            const baseName = file.name.replace(/\.[^/.]+$/, "");
            const newName = `${baseName}_optimized.jpg`;
            const compressedFile = new File([blob], newName, {
              type: 'image/jpeg',
              lastModified: Date.now()
            });
            resolve(compressedFile);
          } else {
            resolve(file); // Fallback to original
          }
        }, 'image/jpeg', quality);
      };
      img.onerror = () => resolve(file);
      img.src = e.target.result;
    };
    reader.onerror = () => resolve(file);
    reader.readAsDataURL(file);
  });
}
