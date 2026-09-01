mkdir video
cd video
mkdir hope
cd ..
ffmpeg  -accurate_seek -i youtube/zxBiv7jk4Zs.mp4 -ss 0  -vf scale=480:-1    concat/01.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke7150.mp4 -ss 390 -to 569   -vf scale=480:-1  concat/01-1.mp4
ffmpeg -f concat -safe 0 -i concat/hope01.txt -c copy video/hope/01.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke18943.mp4 -ss 0 -to 108   -vf scale=480:-1  concat/02-1.mp4
ffmpeg -f concat -safe 0 -i concat/hope02.txt -c copy video/hope/02.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke19144.mp4 -ss 0 -to 81   -vf scale=480:-1  concat/03-1.mp4
ffmpeg -f concat -safe 0 -i concat/hope03.txt -c copy video/hope/03.mp4
ffmpeg  -accurate_seek -i lumo/LUMOMatthew18101930.mp4 -ss 108 -to 235   -vf scale=480:-1  concat/04-1.mp4
ffmpeg -f concat -safe 0 -i concat/hope04.txt -c copy video/hope/04.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke22392325.mp4 -ss 280 -to 552   -vf scale=480:-1  concat/05-1.mp4
ffmpeg -f concat -safe 0 -i concat/hope05.txt -c copy video/hope/05.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke24153.mp4 -ss 0 -to 171   -vf scale=480:-1  concat/06-1.mp4
ffmpeg -f concat -safe 0 -i concat/hope06.txt -c copy video/hope/06.mp4
ffmpeg  -accurate_seek -i lumo/LUMOLuke15111631.mp4 -ss 0 -to 225   -vf scale=480:-1  concat/07-1.mp4
ffmpeg -f concat -safe 0 -i concat/hope07.txt -c copy video/hope/07.mp4
