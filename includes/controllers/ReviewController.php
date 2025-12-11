<?php

class ReviewController extends Controller
{
    public function store(int $productId, int $userId, int $rating, ?string $comment = null): array
    {
        if ($rating < 1 || $rating > 5) {
            return ['success' => false, 'message' => 'Điểm đánh giá không hợp lệ'];
        }

        $reviewModel = new ReviewModel($this->db);
        $saved = $reviewModel->create($productId, $userId, $rating, $comment);

        return [
            'success' => $saved,
            'message' => $saved ? 'Đã gửi đánh giá' : 'Không thể lưu đánh giá',
        ];
    }
}

