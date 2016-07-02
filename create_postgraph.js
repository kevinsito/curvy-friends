function() createPostGraphData(friendsToMe, meToFriends) {
	


}



class GraphMethods {
	protected $postGraphData = null;
	protected $commentGraphData = null;
	protected $interactionGraphData = null;

	public function getPostGraphData() {
		return $this->postGraphData;
	}

	public function getCommentGraphData() {
		return $this->commentGraphData;
	}

	public function getInteractionGraphData() {
		return $this->interactionGraphData;
	}

	public function createPostGraphData($friendsToMe, $meToFriends) {
		if ($this->postGraphData === null) {
			$this->postGraphData = array();

			$currentDate = time() - 604800;

			$toMeCounter = 0;
			$fromMeCounter = 0;

			while (count($friendsToMe) != 0 || count($meToFriends) != 0) {
				if (count($friendsToMe) != 0 && ($friendsToMe[0]['created_time'] - $currentDate) >= 0) {
					array_shift($friendsToMe);
					$toMeCounter++;
				}

				if (count($meToFriends) != 0 && ($meToFriends[0]['created_time'] - $currentDate) >= 0) {
					array_shift($meToFriends);
					$fromMeCounter++;
				}

				if ((count($friendsToMe) == 0 || ($friendsToMe[0]['created_time'] - $currentDate) < 0) && (count($meToFriends) == 0 || ($meToFriends[0]['created_time'] - $currentDate) < 0)) {
					array_unshift($this->postGraphData, array(gmdate("M d, Y", $currentDate), $toMeCounter + $fromMeCounter, $toMeCounter, $fromMeCounter));
					$toMeCounter = 0;
					$fromMeCounter = 0;
					$currentDate -= 604800;
				}
			}
		}

		return $this->postGraphData;
	}

	public function createCommentGraphData($friendsToMe, $meToFriends) {
		if ($this->commentGraphData === null) {
			$this->commentGraphData = array();

			$currentDate = time() - 604800;

			$toMeCounter = 0;
			$fromMeCounter = 0;

			while (count($friendsToMe) != 0 || count($meToFriends) != 0) {
				if (count($friendsToMe) != 0 && ($friendsToMe[0]['created_time'] - $currentDate) >= 0) {
					array_shift($friendsToMe);
					$toMeCounter++;
				}

				if (count($meToFriends) != 0 && ($meToFriends[0]['created_time'] - $currentDate) >= 0) {
					array_shift($meToFriends);
					$fromMeCounter++;
				}

				if ((count($friendsToMe) == 0 || ($friendsToMe[0]['created_time'] - $currentDate) < 0) && (count($meToFriends) == 0 || ($meToFriends[0]['created_time'] - $currentDate) < 0)) {
					array_unshift($this->commentGraphData, array(gmdate("M d, Y", $currentDate), $toMeCounter + $fromMeCounter, $toMeCounter, $fromMeCounter));
					$toMeCounter = 0;
					$fromMeCounter = 0;
					$currentDate -= 604800;
				}
			}
		}

		return $this->commentGraphData;
	}

	public function createInteractionGraphData() {
		if($this->interactionGraphData === null) {
			$this->interactionGraphData = array();
			$copyOfPostData = $this->postGraphData;
			$copyOfCommentData = $this->commentGraphData;

			while (count($copyOfPostData) != 0 || count($copyOfCommentData) != 0) {
				if (count($copyOfPostData) != 0 && count($copyOfCommentData) != 0) {
					$lastPost = array_pop($copyOfPostData);
					$lastComment = array_pop($copyOfCommentData);
					array_unshift($this->interactionGraphData, array($lastPost[0], $lastPost[1] + $lastComment[1], $lastPost[2] + $lastComment[2], $lastPost[3] + $lastComment[3]));
				}
				else if (count($copyOfPostData) != 0) {
					$lastPost = array_pop($copyOfPostData);
					array_unshift($this->interactionGraphData, array($lastPost[0], $lastPost[1], $lastPost[2], $lastPost[3]));
				}
				else if (count($copyOfCommentData) != 0) {
					$lastComment = array_pop($copyOfCommentData);
					array_unshift($this->interactionGraphData, array($lastComment[0], $lastComment[1], $lastComment[2], $lastComment[3]));
				}
			}
		}

		return $this->interactionGraphData;
	}
}
