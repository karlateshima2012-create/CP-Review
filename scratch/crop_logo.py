from PIL import Image

im = Image.open('/Users/karlateshima/Developer/CP REVIEW/public/icon-512.png')
width, height = im.size

# 1. Collect the element pixels starting at y=357 to exclude the speech bubble tip
elements = []
for y in range(357, 820):
    for x in range(300, 724):
        c_left = im.getpixel((260, y))
        c_right = im.getpixel((760, y))
        
        factor = (x - 260) / (760 - 260)
        bg_r = c_left[0] + (c_right[0] - c_left[0]) * factor
        bg_g = c_left[1] + (c_right[1] - c_left[1]) * factor
        bg_b = c_left[2] + (c_right[2] - c_left[2]) * factor
        
        actual = im.getpixel((x, y))
        
        # Calculate alpha
        alpha_r = (actual[0] - bg_r) / (255.0 - bg_r) if (255.0 - bg_r) != 0 else 0
        alpha_g = (actual[1] - bg_g) / (255.0 - bg_g) if (255.0 - bg_g) != 0 else 0
        alpha_b = (actual[2] - bg_b) / (255.0 - bg_b) if (255.0 - bg_b) != 0 else 0
        
        alpha = (alpha_r + alpha_g + alpha_b) / 3.0
        alpha = max(0.0, min(1.0, alpha))
        
        if alpha > 0.01:
            elements.append((x, y, alpha))

print(f"Collected {len(elements)} element pixels.")

# We will create a fresh copy of the image to modify
out_im = im.copy()

# 2. Erase the interior safe box [260, 760] x [200, 820] by interpolating the background
for y in range(200, 820):
    c_left = im.getpixel((260, y))
    c_right = im.getpixel((760, y))
    for x in range(261, 760):
        factor = (x - 260) / (760 - 260)
        bg_r = int(round(c_left[0] + (c_right[0] - c_left[0]) * factor))
        bg_g = int(round(c_left[1] + (c_right[1] - c_left[1]) * factor))
        bg_b = int(round(c_left[2] + (c_right[2] - c_left[2]) * factor))
        out_im.putpixel((x, y), (bg_r, bg_g, bg_b))

# 3. Draw the shifted elements on top
# We shift by delta_y = -74 pixels
delta_y = -74

for x, y, alpha in elements:
    new_y = y + delta_y
    # Interpolate the new background at the target position
    c_left = im.getpixel((260, new_y))
    c_right = im.getpixel((760, new_y))
    
    factor = (x - 260) / (760 - 260)
    bg_r = c_left[0] + (c_right[0] - c_left[0]) * factor
    bg_g = c_left[1] + (c_right[1] - c_left[1]) * factor
    bg_b = c_left[2] + (c_right[2] - c_left[2]) * factor
    
    # Blend white (255, 255, 255) onto the background with alpha
    out_r = int(round((1 - alpha) * bg_r + alpha * 255))
    out_g = int(round((1 - alpha) * bg_g + alpha * 255))
    out_b = int(round((1 - alpha) * bg_b + alpha * 255))
    
    out_im.putpixel((x, new_y), (out_r, out_g, out_b))

# Save the final image directly to public/icon-512.png
out_im.save('/Users/karlateshima/Developer/CP REVIEW/public/icon-512.png')
print("Successfully generated the clean, centered image!")
